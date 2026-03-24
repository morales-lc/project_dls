@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Catalog Management')

@section('content')
<div class="py-5">
    <div class="card shadow rounded-4 p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h4 fw-bold mb-0">Catalog Management</h2>
            <a href="{{ route('catalogs.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Catalog
            </a>
        </div>

        <form method="GET" action="{{ route('catalogs.manage') }}" class="row g-2 mb-3">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Search title, subjects, details, author, ISBN...">
            </div>
            <div class="col-md-2">
                <input type="text" name="year" class="form-control" value="{{ request('year') }}" placeholder="Year">
            </div>
            <div class="col-md-2">
                <input type="text" name="format" class="form-control" value="{{ request('format') }}" placeholder="Format">
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <div class="form-check" title="Require all words to match">
                    <input class="form-check-input" type="checkbox" value="and" id="modeAnd" name="mode" {{ request('mode') === 'and' ? 'checked' : '' }}>
                    <label class="form-check-label small" for="modeAnd">All</label>
                </div>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-outline-secondary">Search</button>
            </div>
            <div class="col-md-1 d-grid">
                <a href="{{ route('catalogs.manage') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <div class="small text-muted mb-3">
            Showing {{ $catalogs->firstItem() ?? 0 }}-{{ $catalogs->lastItem() ?? 0 }} of {{ $catalogs->total() }} results
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Inventory</th>
                        <th>Call Number</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($catalogs as $catalog)
                        @php
                            $copies = is_null($catalog->copies_count) ? null : max((int) $catalog->copies_count, 0);
                            $borrowed = max((int) ($catalog->borrowed_count ?? 0), 0);
                            $available = is_null($copies) ? null : max($copies - $borrowed, 0);
                        @endphp
                        <tr>
                            <td>
                                <img
                                    src="{{ $catalog->cover_image ? asset('storage/' . $catalog->cover_image) : asset('images/book-placeholder.png') }}"
                                    alt="Cover"
                                    style="width:44px;height:64px;object-fit:cover;border-radius:6px;"
                                >
                            </td>
                            <td>{{ $catalog->title }}</td>
                            <td>{{ $catalog->author ?? '-' }}</td>
                            <td>
                                @if(is_null($copies))
                                    <span class="badge bg-warning-subtle text-warning border">No copy information available</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-primary">Total: {{ $copies }}</span>
                                        <span class="badge bg-warning text-dark">Borrowed: {{ min($borrowed, $copies) }}</span>
                                        <span class="badge {{ $available > 0 ? 'bg-success' : 'bg-danger' }}">Available: {{ $available }}</span>
                                        @if($copies > 0 && $available === 0)
                                            <span class="badge bg-danger-subtle text-danger">All copies borrowed</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>{{ $catalog->call_number ?? '-' }}</td>
                            <td>{{ $catalog->year ?? '-' }}</td>
                            <td>
                                <a href="{{ route('catalogs.edit', $catalog->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('catalogs.show', $catalog->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No catalog records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $catalogs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
