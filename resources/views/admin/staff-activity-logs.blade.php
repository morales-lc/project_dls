@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Staff Activity Logs')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold text-pink mb-0">Staff Activity Logs</h2>
            <small class="text-muted">Admin and librarian action trail</small>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.staff.activity.logs.export', request()->query()) }}">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
            <a class="btn btn-success btn-sm" href="{{ route('admin.staff.activity.logs.export.xlsx', request()->query()) }}">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-blue solid">
                <div class="card-body">
                    <div class="text-uppercase small">Total Actions</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-indigo solid">
                <div class="card-body">
                    <div class="text-uppercase small">Admin Actions</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['admin_actions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-teal solid">
                <div class="card-body">
                    <div class="text-uppercase small">Librarian Actions</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['librarian_actions'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="librarian" {{ $role === 'librarian' ? 'selected' : '' }}>Librarian</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Method</label>
                    <select name="method" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $httpMethod)
                            <option value="{{ $httpMethod }}" {{ $method === $httpMethod ? 'selected' : '' }}>{{ $httpMethod }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Action</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['viewed', 'added', 'updated', 'deleted', 'managed', 'imported', 'exported', 'toggled'] as $activityAction)
                            <option value="{{ $activityAction }}" {{ ($action ?? '') === $activityAction ? 'selected' : '' }}>{{ ucfirst($activityAction) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ (string)$userId === (string)$u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm" />
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm" />
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1">Rows</label>
                    <select name="per_page" class="form-select form-select-sm">
                        @foreach([10, 20, 30, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int)($perPage ?? 30) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1">Search (description, path, route, name, email, IP)</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Type keyword..." />
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                    <a href="{{ route('admin.staff.activity.logs') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Method</th>
                            <th>Activity</th>
                            <th>Status</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('M d, Y h:i:s A') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $log->user->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $log->user->email ?? '-' }}</small>
                                </td>
                                <td class="text-capitalize">{{ $log->role }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $log->method }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $log->description ?: ucfirst($log->action ?: 'viewed') . ' ' . ($log->subject_type ?: 'resource') }}</div>
                                    <small class="text-muted">{{ $log->subject_type ?: '-' }}</small>
                                </td>
                                <td>{{ $log->status_code }}</td>
                                <td>{{ $log->ip_address ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No activity logs found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
