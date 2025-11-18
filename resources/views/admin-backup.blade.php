@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-pink mb-4">System Backup</h2>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('backup_output'))
        <details class="mb-3">
            <summary class="btn btn-sm btn-outline-secondary">View Backup Output</summary>
            <pre class="alert alert-secondary mt-2 p-3" style="max-height: 400px; overflow-y: auto;">{{ session('backup_output') }}</pre>
        </details>
    @endif
    <form method="POST" action="{{ route('admin.backup.run') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Backup Type</label>
            <select name="type" class="form-select w-auto d-inline-block">
                <option value="full">Full System Backup</option>
                <option value="database">Database Only</option>
                <option value="files">Project Files Only</option>
            </select>
            <button type="submit" class="btn btn-pink ms-2">Run Backup</button>
        </div>
    </form>
    <hr>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        <strong>Note:</strong> Backups are automatically downloaded to your browser after creation and removed from the server to save disk space.
    </div>
    
    <h5 class="fw-semibold mt-4">Backup History</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Filename</th>
                    <th>Size</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($log->type) }}</span>
                        </td>
                        <td>
                            @if($log->status === 'success')
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $log->filename ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @if($log->file_size_mb)
                                {{ number_format($log->file_size_mb, 2) }} MB
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($log->user)
                                {{ $log->user->name }}
                            @else
                                System
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#logModal{{ $log->id }}">
                                View Log
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Log Modal -->
                    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Backup Log - {{ $log->created_at->format('M d, Y h:i A') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <pre class="bg-light p-3" style="max-height: 400px; overflow-y: auto;">{{ $log->output }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No backup logs yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(session('download_backup'))
<script>
    // Auto-download the backup file using hidden iframe to prevent page navigation
    window.addEventListener('load', function() {
        const downloadUrl = '{{ route("admin.backup.download.auto", ["file" => basename(session("download_backup"))]) }}';
        
        // Create a temporary link and trigger click
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = '{{ basename(session("download_backup")) }}';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
@endif
@endsection
