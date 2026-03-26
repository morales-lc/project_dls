<?php

namespace App\Http\Controllers;

use App\Exports\LoginAnalyticsExport;
use App\Models\UserLoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdminLoginAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->input('mode', 'year');
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $perPage = (int) $request->input('per_page', 20);
        if ($perPage < 10) {
            $perPage = 10;
        }
        if ($perPage > 200) {
            $perPage = 200;
        }

        [$start, $end, $timeLabel, $mode] = $this->resolveTimeframe($mode, $year, $month, $startDateInput, $endDateInput);

        $baseQuery = UserLoginLog::query()
            ->whereIn('role', ['student', 'faculty'])
            ->whereBetween('logged_in_at', [$start, $end]);

        $summary = [
            'total_logins' => (clone $baseQuery)->count(),
            'unique_users' => (clone $baseQuery)->distinct('user_id')->count('user_id'),
            'student_logins' => (clone $baseQuery)->where('role', 'student')->count(),
            'faculty_logins' => (clone $baseQuery)->where('role', 'faculty')->count(),
            'unique_students' => (clone $baseQuery)->where('role', 'student')->distinct('user_id')->count('user_id'),
            'unique_faculty' => (clone $baseQuery)->where('role', 'faculty')->distinct('user_id')->count('user_id'),
        ];

        $studentByProgramCourse = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw("COALESCE(NULLIF(TRIM(user_login_logs.course), ''), 'Unassigned') as course_name")
            ->selectRaw('COUNT(*) as total_logins')
            ->selectRaw('COUNT(DISTINCT user_login_logs.user_id) as unique_users')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->where('user_login_logs.role', 'student')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name', 'course_name')
            ->orderBy('program_name')
            ->orderBy('course_name')
            ->paginate($perPage, ['*'], 'students_page')
            ->withQueryString();

        $facultyByProgram = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw('COUNT(*) as total_logins')
            ->selectRaw('COUNT(DISTINCT user_login_logs.user_id) as unique_users')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->where('user_login_logs.role', 'faculty')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name')
            ->orderBy('program_name')
            ->paginate($perPage, ['*'], 'faculty_page')
            ->withQueryString();

        $recentLogs = UserLoginLog::query()
            ->with(['user', 'studentFaculty.program'])
            ->whereIn('role', ['student', 'faculty'])
            ->whereBetween('logged_in_at', [$start, $end])
            ->orderByDesc('logged_in_at')
            ->paginate($perPage, ['*'], 'logs_page')
            ->withQueryString();

        $programChartRows = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw('COUNT(*) as total_logins')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->whereIn('user_login_logs.role', ['student', 'faculty'])
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name')
            ->orderBy('program_name')
            ->get();

        $courseChartRows = UserLoginLog::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(user_login_logs.course), ''), 'Unassigned') as course_name")
            ->selectRaw('COUNT(*) as total_logins')
            ->where('user_login_logs.role', 'student')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('course_name')
            ->orderBy('course_name')
            ->get();

        $programChartLabels = $programChartRows->pluck('program_name')->values();
        $programChartValues = $programChartRows->pluck('total_logins')->map(fn ($v) => (int) $v)->values();
        $courseChartLabels = $courseChartRows->pluck('course_name')->values();
        $courseChartValues = $courseChartRows->pluck('total_logins')->map(fn ($v) => (int) $v)->values();

        return view('admin.login-analytics', compact(
            'mode',
            'year',
            'month',
            'perPage',
            'startDateInput',
            'endDateInput',
            'timeLabel',
            'summary',
            'studentByProgramCourse',
            'facultyByProgram',
            'recentLogs',
            'programChartLabels',
            'programChartValues',
            'courseChartLabels',
            'courseChartValues'
        ));
    }

    public function export(Request $request)
    {
        $mode = $request->input('mode', 'year');
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        [$start, $end, $timeLabel] = $this->resolveTimeframe($mode, $year, $month, $startDateInput, $endDateInput);

        $baseQuery = UserLoginLog::query()
            ->whereIn('role', ['student', 'faculty'])
            ->whereBetween('logged_in_at', [$start, $end]);

        $summary = [
            'total_logins' => (clone $baseQuery)->count(),
            'unique_users' => (clone $baseQuery)->distinct('user_id')->count('user_id'),
            'student_logins' => (clone $baseQuery)->where('role', 'student')->count(),
            'faculty_logins' => (clone $baseQuery)->where('role', 'faculty')->count(),
            'unique_students' => (clone $baseQuery)->where('role', 'student')->distinct('user_id')->count('user_id'),
            'unique_faculty' => (clone $baseQuery)->where('role', 'faculty')->distinct('user_id')->count('user_id'),
        ];

        $studentByProgramCourse = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw("COALESCE(NULLIF(TRIM(user_login_logs.course), ''), 'Unassigned') as course_name")
            ->selectRaw('COUNT(*) as total_logins')
            ->selectRaw('COUNT(DISTINCT user_login_logs.user_id) as unique_users')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->where('user_login_logs.role', 'student')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name', 'course_name')
            ->orderBy('program_name')
            ->orderBy('course_name')
            ->get();

        $facultyByProgram = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw('COUNT(*) as total_logins')
            ->selectRaw('COUNT(DISTINCT user_login_logs.user_id) as unique_users')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->where('user_login_logs.role', 'faculty')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name')
            ->orderBy('program_name')
            ->get();

        $recentLogs = UserLoginLog::query()
            ->with(['user', 'studentFaculty.program'])
            ->whereIn('role', ['student', 'faculty'])
            ->whereBetween('logged_in_at', [$start, $end])
            ->orderByDesc('logged_in_at')
            ->get();

        $filenameSafeLabel = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($timeLabel));
        $filename = sprintf('login_analytics_%s.csv', $filenameSafeLabel);

        return response()->streamDownload(function () use ($summary, $studentByProgramCourse, $facultyByProgram, $recentLogs, $timeLabel, $start, $end) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Login Analytics Export']);
            fputcsv($output, ['Timeframe', $timeLabel]);
            fputcsv($output, ['Start date', $start->format('M d, Y')]);
            fputcsv($output, ['End date', $end->format('M d, Y')]);
            fputcsv($output, ['Generated at', now()->format('M d, Y h:i A')]);
            fputcsv($output, []);

            fputcsv($output, ['Summary']);
            fputcsv($output, ['Metric', 'Value']);
            fputcsv($output, ['Total Login Events', $summary['total_logins']]);
            fputcsv($output, ['Unique Users Logged In', $summary['unique_users']]);
            fputcsv($output, ['Student Login Events', $summary['student_logins']]);
            fputcsv($output, ['Faculty Login Events', $summary['faculty_logins']]);
            fputcsv($output, ['Unique Students', $summary['unique_students']]);
            fputcsv($output, ['Unique Faculty', $summary['unique_faculty']]);
            fputcsv($output, []);

            fputcsv($output, ['Student Logins by Program and Course']);
            fputcsv($output, ['Program', 'Course', 'Unique Students', 'Login Events']);
            foreach ($studentByProgramCourse as $row) {
                fputcsv($output, [
                    $row->program_name,
                    $row->course_name,
                    (int) $row->unique_users,
                    (int) $row->total_logins,
                ]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Faculty Logins by Program']);
            fputcsv($output, ['Program', 'Unique Faculty', 'Login Events']);
            foreach ($facultyByProgram as $row) {
                fputcsv($output, [
                    $row->program_name,
                    (int) $row->unique_users,
                    (int) $row->total_logins,
                ]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Recent Login Logs']);
            fputcsv($output, ['Date and Time', 'User', 'Role', 'Program', 'Course', 'IP']);
            foreach ($recentLogs as $log) {
                $displayName = trim(($log->studentFaculty->first_name ?? '') . ' ' . ($log->studentFaculty->last_name ?? ''));
                $displayName = $displayName !== '' ? $displayName : ($log->user->name ?? 'Unknown');

                fputcsv($output, [
                    optional($log->logged_in_at)->format('Y-m-d H:i:s'),
                    $displayName,
                    $log->role,
                    $log->studentFaculty->program->name ?? 'Unknown',
                    $log->course ?: 'Unassigned',
                    $log->ip_address ?: '-',
                ]);
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

        [$start, $end, $timeLabel] = $this->resolveTimeframe($mode, $year, $month, $startDateInput, $endDateInput);

        $baseQuery = UserLoginLog::query()
            ->whereIn('role', ['student', 'faculty'])
            ->whereBetween('logged_in_at', [$start, $end]);

        $summary = [
            'Total Login Events' => (clone $baseQuery)->count(),
            'Unique Users Logged In' => (clone $baseQuery)->distinct('user_id')->count('user_id'),
            'Student Login Events' => (clone $baseQuery)->where('role', 'student')->count(),
            'Faculty Login Events' => (clone $baseQuery)->where('role', 'faculty')->count(),
            'Unique Students' => (clone $baseQuery)->where('role', 'student')->distinct('user_id')->count('user_id'),
            'Unique Faculty' => (clone $baseQuery)->where('role', 'faculty')->distinct('user_id')->count('user_id'),
        ];

        $studentByProgramCourse = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw("COALESCE(NULLIF(TRIM(user_login_logs.course), ''), 'Unassigned') as course_name")
            ->selectRaw('COUNT(*) as total_logins')
            ->selectRaw('COUNT(DISTINCT user_login_logs.user_id) as unique_users')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->where('user_login_logs.role', 'student')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name', 'course_name')
            ->orderBy('program_name')
            ->orderBy('course_name')
            ->get();

        $facultyByProgram = UserLoginLog::query()
            ->selectRaw('COALESCE(programs.name, "Unknown") as program_name')
            ->selectRaw('COUNT(*) as total_logins')
            ->selectRaw('COUNT(DISTINCT user_login_logs.user_id) as unique_users')
            ->leftJoin('programs', 'user_login_logs.program_id', '=', 'programs.id')
            ->where('user_login_logs.role', 'faculty')
            ->whereBetween('user_login_logs.logged_in_at', [$start, $end])
            ->groupBy('program_name')
            ->orderBy('program_name')
            ->get();

        $recentLogs = UserLoginLog::query()
            ->with(['user', 'studentFaculty.program'])
            ->whereIn('role', ['student', 'faculty'])
            ->whereBetween('logged_in_at', [$start, $end])
            ->orderByDesc('logged_in_at')
            ->get();

        $summaryRows = [];
        foreach ($summary as $metric => $value) {
            $summaryRows[] = [$metric, $value];
        }

        $studentRows = [];
        foreach ($studentByProgramCourse as $row) {
            $studentRows[] = [
                $row->program_name,
                $row->course_name,
                (int) $row->unique_users,
                (int) $row->total_logins,
            ];
        }

        $facultyRows = [];
        foreach ($facultyByProgram as $row) {
            $facultyRows[] = [
                $row->program_name,
                (int) $row->unique_users,
                (int) $row->total_logins,
            ];
        }

        $recentRows = [];
        foreach ($recentLogs as $log) {
            $displayName = trim(($log->studentFaculty->first_name ?? '') . ' ' . ($log->studentFaculty->last_name ?? ''));
            $displayName = $displayName !== '' ? $displayName : ($log->user->name ?? 'Unknown');

            $recentRows[] = [
                optional($log->logged_in_at)->format('Y-m-d H:i:s'),
                $displayName,
                $log->role,
                $log->studentFaculty->program->name ?? 'Unknown',
                $log->course ?: 'Unassigned',
                $log->ip_address ?: '-',
            ];
        }

        $filename = 'login_analytics_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LoginAnalyticsExport(
            $timeLabel,
            $start,
            $end,
            $summaryRows,
            $studentRows,
            $facultyRows,
            $recentRows
        ), $filename);
    }

    private function resolveTimeframe(string $mode, int $year, int $month, ?string $startDateInput, ?string $endDateInput): array
    {
        if ($mode === 'month') {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $timeLabel = Carbon::create($year, $month, 1)->format('F Y');

            return [$start, $end, $timeLabel, $mode];
        }

        if ($mode === 'semester') {
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

            $timeLabel = $start->format('M d, Y') . ' - ' . $end->format('M d, Y');

            return [$start, $end, $timeLabel, $mode];
        }

        $mode = 'year';
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end = Carbon::create($year, 12, 31)->endOfDay();
        $timeLabel = 'Year ' . $year;

        return [$start, $end, $timeLabel, $mode];
    }
}
