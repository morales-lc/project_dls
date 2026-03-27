@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title', 'Yearbook Archive Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0 text-pink" style="letter-spacing: 1px; font-size: 2rem;">Yearbook Archive Management</h2>
            <a href="{{ route('yearbook.create') }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;">
                <i class="bi bi-plus-lg"></i> Add Yearbook
            </a>
        </div>

        @if(session('status'))
            <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
        @endif

        <form method="GET" action="{{ route('yearbook.manage') }}" class="row g-2 mb-3 align-items-end">
            <div class="col-md-8">
                <label for="search" class="form-label small mb-1">Search by title or year</label>
                <input type="search" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Search yearbooks...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-dark w-100" type="submit">Filter</button>
                <a href="{{ route('yearbook.manage') }}" class="btn btn-pink w-100">Clear</a>
            </div>
        </form>

        <div class="card p-3 shadow rounded-4 w-100">
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                    <thead class="table-pink">
                        <tr>
                            <th>Title</th>
                            <th>Year</th>
                            <th>File</th>
                            <th>Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($yearbooks as $yearbook)
                            <tr>
                                <td class="fw-semibold">{{ $yearbook->title }}</td>
                                <td>{{ $yearbook->year }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $yearbook->pdf_file) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-file-earmark-pdf"></i> View
                                    </a>
                                </td>
                                <td>{{ $yearbook->updated_at?->diffForHumans() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('yearbook.edit', $yearbook->id) }}" class="btn btn-sm btn-warning px-3 me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('yearbook.destroy', $yearbook->id) }}" style="display:inline-block;" onsubmit="return confirm('Delete this yearbook? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger px-3">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No yearbooks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Yearbook pagination">
                    {{ $yearbooks->onEachSide(1)->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
