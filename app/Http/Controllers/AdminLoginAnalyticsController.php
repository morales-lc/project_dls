<?php

namespace App\Http\Controllers;

use App\Models\UserLoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'recentLogs'
        ));
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
