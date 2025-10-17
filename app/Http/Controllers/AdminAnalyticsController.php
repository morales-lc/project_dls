<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResourceView;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $documentType = $request->input('type', 'mides'); // mides or sidlak
        // default action depends on document type: mides -> view, sidlak -> download
        $defaultAction = $documentType === 'sidlak' ? 'download' : 'view';
        $action = $request->input('action', $defaultAction); // view or download

        // timeframe mode: year | month | semester (date range)
        $mode = $request->input('mode', 'year');
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Normalize timeframe to a concrete date range and a human label
        $start = null; $end = null; $timeLabel = '';
        if ($mode === 'month') {
            // Specific month within a year
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $timeLabel = Carbon::create($year, $month, 1)->format('F Y');
        } elseif ($mode === 'semester') {
            // Arbitrary date range selected by admin
            if ($startDateInput && $endDateInput) {
                $start = Carbon::parse($startDateInput)->startOfDay();
                $end = Carbon::parse($endDateInput)->endOfDay();
                if ($end->lt($start)) {
                    // swap if out of order
                    [$start, $end] = [$end, $start];
                }
            } else {
                // Default to Jan 1 - Jun 30 of current year
                $start = Carbon::create($year, 1, 1)->startOfDay();
                $end = Carbon::create($year, 6, 30)->endOfDay();
            }
            // Human readable range
            $timeLabel = $start->format('M d, Y') . ' – ' . $end->format('M d, Y');
        } else {
            // default year mode
            $mode = 'year';
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfDay();
            $timeLabel = 'Year ' . $year;
        }

        // Program-level aggregation
        $programCounts = ResourceView::select('program_id', DB::raw('COALESCE(programs.name, "Unknown") as program_name'), 'role', DB::raw('COUNT(*) as total'))
            ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
            ->where('document_type', $documentType)
            ->where('action', $action)
            ->whereBetween('resource_views.created_at', [$start, $end])
            ->groupBy('program_id', 'program_name', 'role')
            ->orderBy('program_name')
            ->get()
            ->groupBy('program_name');

        // Course-level aggregation per program
        $courseCounts = ResourceView::select('program_id', 'course', 'role', DB::raw('COUNT(*) as total'))
            ->where('document_type', $documentType)
            ->where('action', $action)
            ->whereNotNull('course')
            ->whereBetween('resource_views.created_at', [$start, $end])
            ->groupBy('program_id', 'course', 'role')
            ->orderBy('course')
            ->get()
            ->groupBy('program_id');

        $programs = Program::orderBy('name')->get();

        // Monthly and semester aggregates only for YEAR mode (for breakdown widgets and drilldown)
        $monthlyCounts = null; $semester1 = null; $semester2 = null; $monthlyByProgram = [];
        if ($mode === 'year') {
            // Monthly aggregates for the selected year: month number => total
            $monthlyRaw = ResourceView::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->where('document_type', $documentType)
                ->where('action', $action)
                ->whereYear('created_at', $year)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get()
                ->pluck('total', 'month');

            // ensure months 1..12 exist with zero defaults
            $monthlyCounts = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyCounts[$m] = isset($monthlyRaw[$m]) ? (int) $monthlyRaw[$m] : 0;
            }

            // Semester aggregates for the selected year
            $semester1 = 0; // Jan-Jun
            $semester2 = 0; // Jul-Dec
            for ($m = 1; $m <= 6; $m++) $semester1 += $monthlyCounts[$m];
            for ($m = 7; $m <= 12; $m++) $semester2 += $monthlyCounts[$m];

            // Per-program monthly counts (12 months) for the selected year
            $rows = ResourceView::select(
                    DB::raw('COALESCE(programs.name, "Unknown") as program_name'),
                    DB::raw('MONTH(resource_views.created_at) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
                ->where('document_type', $documentType)
                ->where('action', $action)
                ->whereYear('resource_views.created_at', $year)
                ->groupBy('program_name', DB::raw('MONTH(resource_views.created_at)'))
                ->get();

            // initialize all programs with 12 zeros
            foreach ($programs as $p) {
                $monthlyByProgram[$p->name] = array_fill(1, 12, 0);
            }
            // fill from query
            foreach ($rows as $r) {
                $prog = $r->program_name;
                $mo = (int) $r->month;
                $monthlyByProgram[$prog][$mo] = (int) $r->total;
            }
        }

        return view('admin.analytics', compact(
            'programCounts', 'courseCounts', 'programs', 'documentType', 'action',
            'mode', 'year', 'month', 'startDateInput', 'endDateInput', 'timeLabel',
            'monthlyCounts', 'semester1', 'semester2', 'monthlyByProgram'
        ));
    }

    public function export(Request $request)
    {
        // We always include both MIDES (views) and SIDLAK (downloads) together
        $mode = $request->input('mode', 'year');
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Resolve timeframe
        $start = null; $end = null; $timeLabel = '';
        if ($mode === 'month') {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $timeLabel = Carbon::create($year, $month, 1)->format('F Y');
        } elseif ($mode === 'semester') {
            if ($startDateInput && $endDateInput) {
                $start = Carbon::parse($startDateInput)->startOfDay();
                $end = Carbon::parse($endDateInput)->endOfDay();
                if ($end->lt($start)) {
                    [$start, $end] = [$end, $start];
                }
            } else {
                $start = Carbon::create($year, 1, 1)->startOfDay();
                $end = Carbon::create($year, 6, 30)->endOfDay();
            }
            $timeLabel = $start->format('M d, Y') . ' – ' . $end->format('M d, Y');
        } else {
            $mode = 'year';
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfDay();
            $timeLabel = 'Year ' . $year;
        }

        // Program totals (MIDES views + SIDLAK downloads)
        $programTotals = ResourceView::selectRaw(
                "COALESCE(programs.name, 'Unknown') as program_name,\n" .
                "SUM(CASE WHEN document_type = 'mides' AND action = 'view' THEN 1 ELSE 0 END) as mides_total,\n" .
                "SUM(CASE WHEN document_type = 'sidlak' AND action = 'download' THEN 1 ELSE 0 END) as sidlak_total"
            )
            ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
            ->whereBetween('resource_views.created_at', [$start, $end])
            ->groupBy('program_name')
            ->orderBy('program_name')
            ->get();

        // Monthly totals per program (only for Year mode)
        $monthlyByProgramCombined = [];
        if ($mode === 'year') {
            $monthlyRows = ResourceView::selectRaw(
                    "COALESCE(programs.name, 'Unknown') as program_name,\n" .
                    "MONTH(resource_views.created_at) as month,\n" .
                    "SUM(CASE WHEN (document_type = 'mides' AND action = 'view') OR (document_type = 'sidlak' AND action = 'download') THEN 1 ELSE 0 END) as total"
                )
                ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
                ->whereYear('resource_views.created_at', $year)
                ->whereBetween('resource_views.created_at', [$start, $end])
                ->groupBy('program_name', DB::raw('MONTH(resource_views.created_at)'))
                ->get();

            // collect program names from totals and existing programs
            $programNames = Program::orderBy('name')->pluck('name')->toArray();
            $totalsNames = $programTotals->pluck('program_name')->unique()->values()->toArray();
            if (in_array('Unknown', $totalsNames) && !in_array('Unknown', $programNames)) {
                $programNames[] = 'Unknown';
            }

            foreach ($programNames as $name) {
                $monthlyByProgramCombined[$name] = array_fill(1, 12, 0);
            }
            foreach ($monthlyRows as $r) {
                $monthlyByProgramCombined[$r->program_name][(int)$r->month] = (int)$r->total;
            }
        }

        // Course breakdown per program (combined)
        $courseRows = ResourceView::selectRaw(
                "COALESCE(programs.name, 'Unknown') as program_name,\n" .
                "course,\n" .
                "SUM(CASE WHEN document_type = 'mides' AND action = 'view' THEN 1 ELSE 0 END) as mides_total,\n" .
                "SUM(CASE WHEN document_type = 'sidlak' AND action = 'download' THEN 1 ELSE 0 END) as sidlak_total"
            )
            ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
            ->whereNotNull('course')
            ->whereBetween('resource_views.created_at', [$start, $end])
            ->groupBy('program_name', 'course')
            ->orderBy('program_name')
            ->orderBy('course')
            ->get();

        $filenameSafeLabel = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($timeLabel));
    $filename = sprintf('analytics_%s_%s.csv', $mode, $filenameSafeLabel);

        $startHuman = $start->format('M d, Y');
        $endHuman = $end->format('M d, Y');

        return response()->streamDownload(function () use ($programTotals, $monthlyByProgramCombined, $mode, $year, $timeLabel, $courseRows, $startHuman, $endHuman) {
            $output = fopen('php://output', 'w');

            // File header metadata
            fputcsv($output, ['Analytics Export']);
            fputcsv($output, ['Start date', $startHuman]);
            fputcsv($output, ['End date', $endHuman]);
            fputcsv($output, ['Generated at', now()->format('M d, Y h:i A')]);
            fputcsv($output, []);

            // Section 1: Program totals
            fputcsv($output, ['Program totals']);
            fputcsv($output, ['Program', 'MIDES', 'SIDLAK', 'Total']);
            foreach ($programTotals as $r) {
                $combined = (int)$r->mides_total + (int)$r->sidlak_total;
                fputcsv($output, [$r->program_name, (int)$r->mides_total, (int)$r->sidlak_total, $combined]);
            }
            fputcsv($output, []);

            // Section 2: Monthly per program (Year mode)
            if ($mode === 'year') {
                fputcsv($output, ["Monthly totals per program (Year {$year})"]);
                $header = array_merge(['Program'], ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], ['Total']);
                fputcsv($output, $header);
                foreach ($monthlyByProgramCombined as $programName => $months) {
                    $row = [$programName];
                    $sum = 0;
                    for ($m = 1; $m <= 12; $m++) { $sum += ($months[$m] ?? 0); $row[] = ($months[$m] ?? 0); }
                    $row[] = $sum;
                    fputcsv($output, $row);
                }
                fputcsv($output, []);
            }

            // Section 3: Course breakdown per program (program printed once then courses listed)
            fputcsv($output, ['Course breakdown by program']);
            fputcsv($output, ['Program', 'Course', 'MIDES', 'SIDLAK', 'Total']);

            // group rows by program
            $coursesGrouped = [];
            foreach ($courseRows as $r) {
                $coursesGrouped[$r->program_name] = $coursesGrouped[$r->program_name] ?? [];
                $coursesGrouped[$r->program_name][] = $r;
            }
            foreach ($coursesGrouped as $programName => $rows) {
                $first = true;
                foreach ($rows as $row) {
                    $combined = (int)$row->mides_total + (int)$row->sidlak_total;
                    $programCol = $first ? $programName : '';
                    fputcsv($output, [$programCol, $row->course, (int)$row->mides_total, (int)$row->sidlak_total, $combined]);
                    $first = false;
                }
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportXlsx(Request $request)
    {
        $mode = $request->input('mode', 'year');
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        // Resolve timeframe
        $start = null; $end = null; $timeLabel = '';
        if ($mode === 'month') {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $timeLabel = Carbon::create($year, $month, 1)->format('F Y');
        } elseif ($mode === 'semester') {
            if ($startDateInput && $endDateInput) {
                $start = Carbon::parse($startDateInput)->startOfDay();
                $end = Carbon::parse($endDateInput)->endOfDay();
                if ($end->lt($start)) {
                    [$start, $end] = [$end, $start];
                }
            } else {
                $start = Carbon::create($year, 1, 1)->startOfDay();
                $end = Carbon::create($year, 6, 30)->endOfDay();
            }
            $timeLabel = $start->format('M d, Y') . ' – ' . $end->format('M d, Y');
        } else {
            $mode = 'year';
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfDay();
            $timeLabel = 'Year ' . $year;
        }

        // Program totals combined
        $programTotals = ResourceView::selectRaw(
                "COALESCE(programs.name, 'Unknown') as program_name,\n" .
                "SUM(CASE WHEN document_type = 'mides' AND action = 'view' THEN 1 ELSE 0 END) as mides_total,\n" .
                "SUM(CASE WHEN document_type = 'sidlak' AND action = 'download' THEN 1 ELSE 0 END) as sidlak_total"
            )
            ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
            ->whereBetween('resource_views.created_at', [$start, $end])
            ->groupBy('program_name')
            ->orderBy('program_name')
            ->get();

        $programTotalsArr = [];
        foreach ($programTotals as $r) {
            $m = (int)$r->mides_total; $s = (int)$r->sidlak_total;
            $programTotalsArr[] = [
                'program' => $r->program_name,
                'mides' => $m,
                'sidlak' => $s,
                'total' => $m + $s,
            ];
        }

        // Monthly by program for Year mode only
        $monthlyByProgramCombined = null;
        if ($mode === 'year') {
            $monthlyByProgramCombined = [];
            $monthlyRows = ResourceView::selectRaw(
                    "COALESCE(programs.name, 'Unknown') as program_name,\n" .
                    "MONTH(resource_views.created_at) as month,\n" .
                    "SUM(CASE WHEN (document_type = 'mides' AND action = 'view') OR (document_type = 'sidlak' AND action = 'download') THEN 1 ELSE 0 END) as total"
                )
                ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
                ->whereYear('resource_views.created_at', $year)
                ->whereBetween('resource_views.created_at', [$start, $end])
                ->groupBy('program_name', DB::raw('MONTH(resource_views.created_at)'))
                ->get();

            $programNames = Program::orderBy('name')->pluck('name')->toArray();
            $totalsNames = collect($programTotalsArr)->pluck('program')->unique()->values()->toArray();
            if (in_array('Unknown', $totalsNames) && !in_array('Unknown', $programNames)) {
                $programNames[] = 'Unknown';
            }

            foreach ($programNames as $name) {
                $monthlyByProgramCombined[$name] = array_fill(1, 12, 0);
            }
            foreach ($monthlyRows as $r) {
                $monthlyByProgramCombined[$r->program_name][(int)$r->month] = (int)$r->total;
            }
        }

        // Course breakdown grouped by program
        $courseRows = ResourceView::selectRaw(
                "COALESCE(programs.name, 'Unknown') as program_name,\n" .
                "course,\n" .
                "SUM(CASE WHEN document_type = 'mides' AND action = 'view' THEN 1 ELSE 0 END) as mides_total,\n" .
                "SUM(CASE WHEN document_type = 'sidlak' AND action = 'download' THEN 1 ELSE 0 END) as sidlak_total"
            )
            ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
            ->whereNotNull('course')
            ->whereBetween('resource_views.created_at', [$start, $end])
            ->groupBy('program_name', 'course')
            ->orderBy('program_name')
            ->orderBy('course')
            ->get();

        $courseBreakdown = [];
        foreach ($courseRows as $r) {
            $prog = $r->program_name;
            $courseBreakdown[$prog] = $courseBreakdown[$prog] ?? [];
            $m = (int)$r->mides_total; $s = (int)$r->sidlak_total;
            $courseBreakdown[$prog][] = [
                'course' => $r->course,
                'mides' => $m,
                'sidlak' => $s,
                'total' => $m + $s,
            ];
        }

        $filenameSafeLabel = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($timeLabel));
        $filename = sprintf('analytics_%s_%s.xlsx', $mode, $filenameSafeLabel);

        $meta = [
            'mode' => $mode,
            'label' => $timeLabel,
            'start' => $start->format('M d, Y'),
            'end' => $end->format('M d, Y'),
            'generated_at' => now()->format('M d, Y h:i A'),
            'year' => $year,
            'documents' => 'MIDES, SIDLAK',
            'program_count' => count($programTotalsArr),
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new AnalyticsExport($meta, $programTotalsArr, $monthlyByProgramCombined, $courseBreakdown), $filename);
    }
}
