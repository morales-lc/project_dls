@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-pink mb-4">System Backup Management</h2>
    
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

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                <i class="bi bi-play-circle"></i> Manual Backup
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules" type="button" role="tab">
                <i class="bi bi-calendar-check"></i> Scheduled Backups
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="bi bi-clock-history"></i> Backup History
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Manual Backup Tab -->
        <div class="tab-pane fade show active" id="manual" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Run Manual Backup</h5>
                    <form method="POST" action="{{ route('admin.backup.run') }}" class="row g-3" id="manualBackupForm">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Backup Type</label>
                            <select name="type" class="form-select" required>
                                <option value="full">Full System Backup (Database + Files)</option>
                                <option value="database">Database Only</option>
                                <option value="files">Project Files Only</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-pink w-100" id="runBackupBtn">
                                <span id="backupBtnIdle"><i class="bi bi-play-fill"></i> Run Backup Now</span>
                                <span id="backupBtnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Queuing backup...
                                </span>
                            </button>
                        </div>
                    </form>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> Manual backups are queued and processed in the background so you can continue using the system.
                        <small class="d-block mt-2">Ensure queue worker is running: <code>php artisan queue:work --tries=3</code></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduled Backups Tab -->
        <div class="tab-pane fade" id="schedules" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-semibold mb-0">Backup Schedules</h5>
                        <button class="btn btn-pink btn-sm" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                            <i class="bi bi-plus-lg"></i> Add Schedule
                        </button>
                    </div>

                    @if($schedules->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No backup schedules configured yet. Click "Add Schedule" to create one.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Frequency</th>
                                        <th>Backup Type</th>
                                        <th>Time</th>
                                        <th>Retention</th>
                                        <th>Last Run</th>
                                        <th>Next Run</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td>
                                                @if($schedule->enabled)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-calendar"></i> {{ ucfirst($schedule->frequency) }}
                                                </span>
                                            </td>
                                            <td>{{ ucfirst($schedule->backup_type) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($schedule->scheduled_time)->format('h:i A') }}</td>
                                            <td>Keep {{ $schedule->retention_count }}</td>
                                            <td>
                                                @if($schedule->last_run_at)
                                                    <small>{{ $schedule->last_run_at->format('M d, Y h:i A') }}</small>
                                                @else
                                                    <small class="text-muted">Never</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($schedule->next_run_at && $schedule->enabled)
                                                    <small>{{ $schedule->next_run_at->format('M d, Y h:i A') }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <form method="POST" action="{{ route('admin.backup.schedule.toggle', $schedule->id) }}" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-secondary" title="{{ $schedule->enabled ? 'Disable' : 'Enable' }}">
                                                            <i class="bi bi-{{ $schedule->enabled ? 'pause' : 'play' }}-fill"></i>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $schedule->id }}" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.backup.schedule.destroy', $schedule->id) }}" style="display:inline;" onsubmit="return confirm('Delete this schedule? This will not delete existing backups.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Schedule Modal -->
                                        <div class="modal fade" id="editScheduleModal{{ $schedule->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('admin.backup.schedule.update', $schedule->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Backup Schedule</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Frequency <span class="text-danger">*</span></label>
                                                                <select name="frequency" class="form-select" required>
                                                                    <option value="daily" {{ $schedule->frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                                                                    <option value="weekly" {{ $schedule->frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                                                    <option value="monthly" {{ $schedule->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Backup Type <span class="text-danger">*</span></label>
                                                                <select name="backup_type" class="form-select" required>
                                                                    <option value="full" {{ $schedule->backup_type == 'full' ? 'selected' : '' }}>Full (Database + Files)</option>
                                                                    <option value="database" {{ $schedule->backup_type == 'database' ? 'selected' : '' }}>Database Only</option>
                                                                    <option value="files" {{ $schedule->backup_type == 'files' ? 'selected' : '' }}>Files Only</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                                                                <input type="time" name="scheduled_time" class="form-control" value="{{ \Carbon\Carbon::parse($schedule->scheduled_time)->format('H:i') }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Retention Count <span class="text-danger">*</span></label>
                                                                <input type="number" name="retention_count" class="form-control" value="{{ $schedule->retention_count }}" min="1" max="365" required>
                                                                <small class="text-muted">Number of backups to keep (1-365)</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="checkbox" name="enabled" class="form-check-input" id="editEnabled{{ $schedule->id }}" {{ $schedule->enabled ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="editEnabled{{ $schedule->id }}">Enable this schedule</label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update Schedule</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Windows Task Scheduler Setup:</strong> To automate backups, run this command in Task Scheduler:
                        <code class="d-block mt-2 p-2 bg-light">php {{ base_path('artisan') }} backup:scheduled</code>
                        <small class="d-block mt-2">Recommended: Run every hour to check for due backups.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup History Tab -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Backup History</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Source</th>
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
                                        <td><small>{{ $log->created_at->format('M d, Y h:i A') }}</small></td>
                                        <td>
                                            @if($log->frequency === 'manual')
                                                <span class="badge bg-secondary">Manual</span>
                                            @else
                                                <span class="badge bg-primary">{{ ucfirst($log->frequency) }}</span>
                                            @endif
                                        </td>
                                        <td><small>{{ ucfirst($log->type) }}</small></td>
                                        <td>
                                            @if($log->status === 'success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif($log->status === 'processing')
                                                <span class="badge bg-warning text-dark">Processing</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->filename)
                                                <small>{{ $log->filename }}</small>
                                            @else
                                                <small class="text-muted">N/A</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->file_size_mb)
                                                <small>{{ number_format($log->file_size_mb, 2) }} MB</small>
                                            @else
                                                <small class="text-muted">N/A</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <small>{{ $log->user->name }}</small>
                                            @else
                                                <small class="text-muted">System</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#logModal{{ $log->id }}">
                                                    <i class="bi bi-file-text"></i> View Log
                                                </button>
                                                @if($log->status === 'success' && $log->filename)
                                                    <a href="{{ route('admin.backup.download', ['file' => $log->filename]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.backup.file.delete', ['id' => $log->id]) }}" onsubmit="return confirm('Delete this backup file from server storage?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
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
                                        <td colspan="8" class="text-center text-muted">No backup logs yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.backup.schedule.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Backup Schedule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" class="form-select @error('frequency') is-invalid @enderror" required>
                                <option value="">-- Select Frequency --</option>
                                <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">How often the backup should run</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Backup Type <span class="text-danger">*</span></label>
                            <select name="backup_type" class="form-select @error('backup_type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="full" {{ old('backup_type') == 'full' ? 'selected' : '' }}>Full (Database + Files)</option>
                                <option value="database" {{ old('backup_type') == 'database' ? 'selected' : '' }}>Database Only</option>
                                <option value="files" {{ old('backup_type') == 'files' ? 'selected' : '' }}>Files Only</option>
                            </select>
                            @error('backup_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                            <input type="time" name="scheduled_time" class="form-control @error('scheduled_time') is-invalid @enderror" value="{{ old('scheduled_time', '02:00') }}" required>
                            @error('scheduled_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Time when backup should run (default: 2:00 AM)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Retention Count <span class="text-danger">*</span></label>
                            <input type="number" name="retention_count" class="form-control @error('retention_count') is-invalid @enderror" value="{{ old('retention_count', 7) }}" min="1" max="365" required>
                            @error('retention_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of backups to keep (1-365). Older backups will be deleted automatically.</small>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="enabled" class="form-check-input" id="addEnabled" {{ old('enabled', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="addEnabled">Enable this schedule immediately</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-pink">Create Schedule</button>
                    </div>
                </form>
            </div>
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('manualBackupForm');
    const button = document.getElementById('runBackupBtn');
    const idle = document.getElementById('backupBtnIdle');
    const loading = document.getElementById('backupBtnLoading');

    if (!form || !button || !idle || !loading) {
        return;
    }

    form.addEventListener('submit', function () {
        button.disabled = true;
        idle.classList.add('d-none');
        loading.classList.remove('d-none');
    });
});
</script>

@if($errors->any())
<script>
    // Auto-open the modal if there are validation errors
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('addScheduleModal'));
        modal.show();
    });
</script>
@endif
@endsection
