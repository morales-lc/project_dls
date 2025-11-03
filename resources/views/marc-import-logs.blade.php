@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'MARC Import History')

@section('content')
<div class="py-5">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <div>
                <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60;">Import History</h2>
                <small class="text-muted">View all MARC catalog import logs</small>
            </div>
            <div class="d-flex gap-2">
                @if(!$logs->isEmpty())
                    <a href="{{ route('marc.import.logs.export') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export to Excel
                    </a>
                @endif
                <a href="{{ route('marc.import.form') }}" class="btn btn-pink">
                    <i class="bi bi-upload"></i> New Import
                </a>
            </div>
        </div>

        @if($logs->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>No import history yet. Start by importing your first MARC file.
            </div>
        @else
            <div class="card shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Date & Time</th>
                                    <th class="px-4 py-3">Imported By</th>
                                    <th class="px-4 py-3">Filename</th>
                                    <th class="px-4 py-3 text-center">Added</th>
                                    <th class="px-4 py-3 text-center">Updated</th>
                                    <th class="px-4 py-3 text-center">Deleted</th>
                                    <th class="px-4 py-3 text-center">Unchanged</th>
                                    <th class="px-4 py-3 text-center">Errors</th>
                                    <th class="px-4 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="small">{{ $log->created_at->format('M d, Y') }}</div>
                                        <div class="text-muted small">{{ $log->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small fw-semibold">{{ $log->user->name ?? 'Unknown' }}</div>
                                        <div class="text-muted small">{{ $log->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small">{{ $log->filename }}</div>
                                        <div class="text-muted small">{{ $log->total_parsed }} records parsed</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-success">{{ $log->records_added }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-info">{{ $log->records_updated }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($log->deletion_enabled)
                                            <span class="badge bg-danger">{{ $log->records_deleted }}</span>
                                        @else
                                            <span class="badge bg-secondary" title="Deletion was disabled">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-light text-dark">{{ $log->records_unchanged }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($log->records_errors > 0)
                                            <span class="badge bg-warning">{{ $log->records_errors }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">0</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-outline-secondary me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#logModal{{ $log->id }}"
                                            title="View details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($log->log_file_path)
                                            <a 
                                                href="{{ route('marc.log.download', $log->log_file_path) }}" 
                                                class="btn btn-sm btn-outline-primary" 
                                                download
                                                title="Download log file">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal for detailed summary -->
                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Import Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <pre class="bg-light p-3 rounded small mb-0" style="white-space: pre-wrap;">{{ $log->summary }}</pre>
                                            </div>
                                            <div class="modal-footer">
                                                @if($log->log_file_path)
                                                    <a href="{{ route('marc.log.download', $log->log_file_path) }}" class="btn btn-sm btn-primary" download>
                                                        <i class="bi bi-download"></i> Download Full Log
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
