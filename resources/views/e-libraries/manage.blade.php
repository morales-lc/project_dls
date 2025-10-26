@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title', 'Online Databases Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Online Databases Management</h2>
            <a class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;" href="{{ route('e-libraries.create') }}">
                <i class="bi bi-plus-lg"></i> Add New E-Library
            </a>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <!-- Filter & Sort Form -->
        <form method="GET" action="{{ route('e-libraries.manage') }}" class="row g-2 mb-3 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name or text..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="credentials" class="form-select">
                    <option value="">All</option>
                    <option value="with" {{ request('credentials')==='with' ? 'selected' : '' }}>With credentials</option>
                    <option value="without" {{ request('credentials')==='without' ? 'selected' : '' }}>Without credentials</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort','created_at')==='created_at' ? 'selected' : '' }}>Created</option>
                        <option value="updated_at" {{ request('sort')==='updated_at' ? 'selected' : '' }}>Updated</option>
                        <option value="name" {{ request('sort')==='name' ? 'selected' : '' }}>Name</option>
                    </select>
                    <select name="direction" class="form-select">
                        <option value="desc" {{ request('direction','desc')==='desc' ? 'selected' : '' }}>Desc</option>
                        <option value="asc" {{ request('direction')==='asc' ? 'selected' : '' }}>Asc</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                    <a href="{{ route('e-libraries.manage') }}" class="btn btn-pink w-100">Clear</a>
                </div>
            </div>
        </form>

        <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                    <thead class="table-pink">
                        <tr style="font-size:1.05rem;">
                            <th style="width:70px">Logo</th>
                            <th>Name</th>
                            <th>Link</th>
                            <th>Has Credentials</th>
                            <th>Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($libraries as $lib)
                        <tr>
                            <td>
                                @if($lib->image)
                                    <img src="{{ asset('storage/'.$lib->image) }}" alt="Logo" style="width:50px;height:50px;object-fit:cover; border-radius:0.5rem; box-shadow:0 2px 8px 0 rgba(40,40,60,0.10);">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $lib->name }}</td>
                            <td><a href="{{ $lib->link }}" target="_blank" rel="noreferrer noopener" class="btn btn-sm btn-outline-primary px-3">Open</a></td>
                            <td>
                                @if($lib->username || $lib->password)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>{{ $lib->updated_at?->diffForHumans() }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-warning px-3 me-1" href="{{ route('e-libraries.edit', $lib->id) }}">Edit</a>
                                <form action="{{ route('e-libraries.destroy', $lib->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this e-library?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger px-3" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No e-libraries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    {{ $libraries->onEachSide(1)->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>
    
</div>
@endsection
