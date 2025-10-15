@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
@endpush

@section('title', 'Information Literacy Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Information Literacy Control Panel</h2>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('information_literacy.create') }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;">
                <i class="bi bi-plus-lg"></i> Add New Seminar
            </a>
        </div>

        <div style="height: 30px;"></div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Filter & Sort Form -->
        <form method="GET" action="{{ route('information_literacy.manage') }}" class="row g-2 mb-3 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by title, facilitator..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="onsite" {{ request('type') == 'onsite' ? 'selected' : '' }}>Onsite</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="date_time" {{ request('sort') == 'date_time' ? 'selected' : '' }}>Date</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="direction" class="form-select">
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
            </div>
        </form>

        <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                    <thead class="table-pink">
                        <tr style="font-size:1.05rem;">
                            <th>Image</th>
                            <th>Title</th>
                            <th>Date & Time</th>
                            <th>Facilitator/s</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                        <tr>
                            <td>
                                @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" alt="Image" style="width:60px;height:45px;object-fit:cover; border-radius:0.5rem; box-shadow:0 2px 8px 0 rgba(40,40,60,0.10);">
                                @else
                                <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $post->title ?? 'Untitled' }}</td>
                            <td class="text-muted">{{ $post->date_time ?? '-' }}</td>
                            <td class="text-muted" style="white-space: normal; word-wrap: break-word; max-width: 180px;">{{ $post->facilitators ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info text-dark px-3 py-2" style="font-size:0.95rem;">
                                    {{ ucfirst($post->type) ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('information_literacy.edit', $post->id) }}" class="btn btn-sm btn-warning px-3 me-1">Edit</a>
                                <form action="{{ route('information_literacy.delete', $post->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-3" onclick="return confirm('Delete this seminar?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No seminars found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    {{ $posts->onEachSide(1)->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
