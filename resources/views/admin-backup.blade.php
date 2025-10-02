@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-pink mb-4">System Backup</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('backup_output'))
        <pre class="alert alert-secondary">{{ session('backup_output') }}</pre>
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
    <h5 class="fw-semibold">Backup Files</h5>
    <ul class="list-group">
        @forelse($backups as $backup)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ basename($backup) }}</span>
                <a href="{{ route('admin.backup.download', ['file' => urlencode($backup)]) }}" class="btn btn-sm btn-outline-primary">Download</a>
            </li>
        @empty
            <li class="list-group-item">No backups found.</li>
        @endforelse
    </ul>
</div>
@endsection
