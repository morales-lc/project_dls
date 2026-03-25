<?php

namespace App\Http\Controllers;

use App\Exports\StaffActivityLogsExport;
use App\Models\StaffActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminStaffActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 30);
        if ($perPage < 10) {
            $perPage = 10;
        }
        if ($perPage > 200) {
            $perPage = 200;
        }

        $role = $request->input('role');
        $method = $request->input('method');
        $action = $request->input('action');
        $userId = $request->input('user_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = trim((string) $request->input('search', ''));

        $query = $this->buildFilteredQuery($request);

        $logs = (clone $query)->latest()->paginate($perPage)->withQueryString();

        $users = User::query()
            ->whereIn('role', ['admin', 'librarian'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $summary = [
            'total' => (clone $query)->count(),
            'admin_actions' => (clone $query)->where('role', 'admin')->count(),
            'librarian_actions' => (clone $query)->where('role', 'librarian')->count(),
        ];

        return view('admin.staff-activity-logs', compact(
            'logs',
            'users',
            'summary',
            'role',
            'method',
            'action',
            'perPage',
            'userId',
            'dateFrom',
            'dateTo',
            'search'
        ));
    }

    public function export(Request $request)
    {
        $rows = $this->buildFilteredQuery($request)
            ->latest()
            ->get();

        $filename = 'staff_activity_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Staff Activity Logs Export']);
            fputcsv($output, ['Generated at', now()->format('M d, Y h:i A')]);
            fputcsv($output, []);
            fputcsv($output, ['When', 'User', 'Email', 'Role', 'Method', 'Action', 'Activity', 'Subject Type', 'Subject ID', 'Status', 'IP']);

            foreach ($rows as $log) {
                fputcsv($output, [
                    optional($log->created_at)->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'Unknown',
                    $log->user->email ?? '-',
                    $log->role,
                    $log->method,
                    $log->action,
                    $log->description,
                    $log->subject_type,
                    $log->subject_id,
                    $log->status_code,
                    $log->ip_address,
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportXlsx(Request $request)
    {
        $rows = $this->buildFilteredQuery($request)
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'When' => optional($log->created_at)->format('Y-m-d H:i:s'),
                    'User' => $log->user->name ?? 'Unknown',
                    'Email' => $log->user->email ?? '-',
                    'Role' => $log->role,
                    'Method' => $log->method,
                    'Action' => $log->action,
                    'Activity' => $log->description,
                    'Subject Type' => $log->subject_type,
                    'Subject ID' => $log->subject_id,
                    'Status' => $log->status_code,
                    'IP' => $log->ip_address,
                ];
            })
            ->values()
            ->toArray();

        $filename = 'staff_activity_logs_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StaffActivityLogsExport($rows), $filename);
    }

    private function buildFilteredQuery(Request $request)
    {
        $role = $request->input('role');
        $method = $request->input('method');
        $action = $request->input('action');
        $userId = $request->input('user_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = trim((string) $request->input('search', ''));

        $query = StaffActivityLog::query()->with('user');

        if (in_array($role, ['admin', 'librarian'], true)) {
            $query->where('role', $role);
        }

        if (in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $query->where('method', $method);
        }

        if ($action) {
            $query->where('action', $action);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('path', 'like', '%' . $search . '%')
                    ->orWhere('route_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('subject_type', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQ) use ($search) {
                        $userQ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query;
    }
}
