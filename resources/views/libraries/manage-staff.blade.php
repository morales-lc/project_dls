@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Manage Library Staff')

@section('content')
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
                        <tr>
                            <td>
                                <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                            </td>
                            <td>{{ $s->prefix }} {{ $s->first_name }} {{ $s->middlename ? $s->middlename . ' ' : '' }}{{ $s->last_name }}</td>
                            <td>{{ $s->role }}</td>
                            <td><a href="mailto:{{ $s->email }}" class="text-pink">{{ $s->email }}</a></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $s->department)) }}</td>
                            <td>{{ $s->description }}</td>
                            <td>
                                <a href="{{ route('libraries.staff.edit', $s->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('libraries.staff.destroy', $s->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff member?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection