<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResourceView;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $documentType = $request->input('type', 'mides'); // mides or sidlak
        // default action depends on document type: mides -> view, sidlak -> download
        $defaultAction = $documentType === 'sidlak' ? 'download' : 'view';
        $action = $request->input('action', $defaultAction); // view or download

        // range mode: 'year' (default full-year view with monthly breakdown), 'month' (specific month/year), 'sem' (custom date range)
        $range = $request->input('range', 'year');
        // year and month inputs (used when range is 'year' or 'month')
        $year = (int) $request->input('year', date('Y'));
        $month = $request->input('month', null);
        // semester date range inputs (YYYY-MM-DD)
        $semStart = $request->input('sem_start', null);
        $semEnd = $request->input('sem_end', null);

        // compute date filters
        $startDate = null;
        $endDate = null;
        if ($range === 'month' && $month) {
            $startDate = date(sprintf('%04d-%02d-01', $year, (int)$month));
            $endDate = date('Y-m-t', strtotime($startDate));
        } elseif ($range === 'sem' && $semStart && $semEnd) {
            $startDate = date('Y-m-d', strtotime($semStart));
            $endDate = date('Y-m-d', strtotime($semEnd));
        } else {
            // default: full selected year
            $startDate = date(sprintf('%04d-01-01', $year));
            $endDate = date(sprintf('%04d-12-31', $year));
        }

        // Program-level aggregation
        $programQuery = ResourceView::select('program_id', DB::raw('COALESCE(programs.name, "Unknown") as program_name'), 'role', DB::raw('COUNT(*) as total'))
            ->leftJoin('programs', 'resource_views.program_id', '=', 'programs.id')
            ->where('document_type', $documentType)
            ->where('action', $action)
            ->whereBetween('resource_views.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('program_id', 'program_name', 'role')
            ->orderBy('program_name');

        $programCounts = $programQuery->get()->groupBy('program_name');

        // Course-level aggregation per program
        $courseQuery = ResourceView::select('program_id', 'course', 'role', DB::raw('COUNT(*) as total'))
            ->where('document_type', $documentType)
            ->where('action', $action)
            ->whereNotNull('course')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('program_id', 'course', 'role')
            ->orderBy('course');

        $courseCounts = $courseQuery->get()->groupBy('program_id');

        $programs = Program::orderBy('name')->get();

        // Monthly aggregates only when viewing by full year (range === 'year')
        $monthlyCounts = [];
        $semester1 = 0;
        $semester2 = 0;
        if ($range === 'year') {
            $monthlyRaw = ResourceView::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->where('document_type', $documentType)
                ->where('action', $action)
                ->whereYear('created_at', $year)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get()
                ->pluck('total', 'month');

            for ($m = 1; $m <= 12; $m++) {
                $monthlyCounts[$m] = isset($monthlyRaw[$m]) ? (int) $monthlyRaw[$m] : 0;
            }
            // Semester aggregates for the selected year
            for ($m = 1; $m <= 6; $m++) $semester1 += $monthlyCounts[$m];
            for ($m = 7; $m <= 12; $m++) $semester2 += $monthlyCounts[$m];
        } else {
            // If range is month or sem, we can compute semester totals by summing programCounts if desired
            $semester1 = null;
            $semester2 = null;
        }

        return view('admin.analytics', compact('programCounts', 'courseCounts', 'programs', 'documentType', 'action', 'year', 'month', 'range', 'semStart', 'semEnd', 'monthlyCounts', 'semester1', 'semester2'));
    }
}
