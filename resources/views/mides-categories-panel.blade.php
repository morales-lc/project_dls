@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'MIDES Categories Panel')

@section('content')
<div class="py-5">
    <div class="card shadow rounded-4 w-100" style="max-width:1100px;margin:auto; background:#fff;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0 text-pink">Category Control Panel</h2>
                <div>
                    <a href="{{ route('mides.management') }}" class="btn btn-secondary">
                        <i class="bi bi-back-lg"></i> Go Back
                    </a>
                    <a href="#" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-lg"></i> Add Category
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Add Category Modal -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('mides.categories.add') }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Category/Program Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Add Category</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Grouped Categories by Type -->
            <div class="card p-3 shadow-sm rounded-3">
                <div class="table-responsive">
                    @foreach($types as $type)
                        @php
                            $grouped = $categories->where('type', $type);
                        @endphp

                        @if($grouped->count() > 0)
                        <h4 class="fw-bold text-pink mt-4 mb-3">{{ $type }}</h4>
                        <table class="table table-hover align-middle bg-white rounded-4 mb-4">
                            <thead class="table-pink">
                                <tr>
                                    <th style="width: 70%;">Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grouped as $cat)
                                <tr>
                                    <form method="POST" action="{{ route('mides.categories.update', $cat->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <td>
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $cat->name }}">
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-sm btn-warning">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('mides.categories.delete', $cat->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                        </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('mides.management') }}" class="btn btn-outline-secondary">&larr; Back to Repository Management</a>
            </div>
        </div>
    </div>
</div>
@endsection
