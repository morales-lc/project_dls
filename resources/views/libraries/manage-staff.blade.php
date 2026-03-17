@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Manage Library Staff')

@section('content')
<style>
    .staff-row {
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .staff-row:hover {
        background-color: #fff0f5;
        transform: translateY(-2px);
    }
</style>

<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fw-bold mb-0 text-pink" style="letter-spacing: 1px; font-size: 2rem;">Manage Library Staff</h2>

        </div>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="d-flex">

            <a href="{{ route('libraries.staff.create') }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;"><i class="bi bi-plus-lg"></i> Add Staff</a>
            
        </div>
        
        <div style="height: 30px;"></div>
        <div class="card p-4 shadow rounded-4 w-100">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle bg-white rounded-4">
                    <thead class="table-pink">
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Role/Position</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $s)
                        <tr class="staff-row">
                            <td data-bs-toggle="modal" data-bs-target="#staffModal{{ $s->id }}" style="cursor: pointer;">
                                <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                            </td>
                            <td data-bs-toggle="modal" data-bs-target="#staffModal{{ $s->id }}" style="cursor: pointer;">{{ $s->prefix }} {{ $s->first_name }} {{ $s->middlename ? $s->middlename . ' ' : '' }}{{ $s->last_name }}</td>
                            <td data-bs-toggle="modal" data-bs-target="#staffModal{{ $s->id }}" style="cursor: pointer;">{{ $s->role }}</td>
                            <td data-bs-toggle="modal" data-bs-target="#staffModal{{ $s->id }}" style="cursor: pointer;"><a href="mailto:{{ $s->email }}" class="text-pink" onclick="event.stopPropagation();">{{ $s->email }}</a></td>
                            <td data-bs-toggle="modal" data-bs-target="#staffModal{{ $s->id }}" style="cursor: pointer;">{{ ucfirst(str_replace('_', ' ', $s->department)) }}</td>
                            <td data-bs-toggle="modal" data-bs-target="#staffModal{{ $s->id }}" style="cursor: pointer;">{{ Str::limit($s->description, 50) }}</td>
                            <td>
                                <a href="{{ route('libraries.staff.edit', $s->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('libraries.staff.destroy', $s->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff member?')">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Staff Details Modal -->
                        <div class="modal fade" id="staffModal{{ $s->id }}" tabindex="-1" aria-labelledby="staffModalLabel{{ $s->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header" style="background: linear-gradient(135deg, #f8bbd0, #f48fb1); color: #4a0033;">
                                        <h5 class="modal-title fw-bold" id="staffModalLabel{{ $s->id }}">Staff Information</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="text-center mb-4">
                                            <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}" 
                                                 class="rounded-circle shadow-sm mb-3" 
                                                 style="width:150px;height:150px;object-fit:cover;">
                                            <h4 class="fw-bold mb-1" style="color:#880e4f;">
                                                {{ $s->prefix }} {{ $s->first_name }} {{ $s->middlename ? $s->middlename . ' ' : '' }}{{ $s->last_name }}
                                            </h4>
                                            <div class="text-muted mb-2">{{ $s->role }}</div>
                                        </div>
                                        <hr>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="small text-uppercase text-muted mb-1">Email</div>
                                                <div><a href="mailto:{{ $s->email }}" class="text-pink">{{ $s->email }}</a></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-uppercase text-muted mb-1">Department</div>
                                                <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $s->department)) }}</div>
                                            </div>
                                            <div class="col-12">
                                                <div class="small text-uppercase text-muted mb-1">Description of Work</div>
                                                <div class="p-3 rounded" style="background-color: #f8f9fa;">
                                                    {{ $s->description ?: 'No description provided.' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-between">
                                        <div>
                                            <a href="{{ route('libraries.staff.edit', $s->id) }}" class="btn btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('libraries.staff.destroy', $s->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this staff member?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
</div>
@endsection