@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Edit Library Staff')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 800px; background: #fff;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('libraries.staff.manage') }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Management</a>
            <span></span>
        </div>

        <h2 class="fw-bold mb-4 text-center" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Edit Library Staff</h2>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        <form method="POST" action="{{ route('libraries.staff.update', $staff->id) }}" enctype="multipart/form-data" class="row g-4">
            @csrf
            @method('PUT')

            <div class="col-12">
                
                @if($staff->photo)
                <div class="mt-3 text-center">
                    <img src="{{ asset('storage/' . $staff->photo) }}" class="rounded-circle shadow-sm" style="width:100px; height:100px; object-fit:cover;">
                </div>
                @endif
            </div>

            <div class="col-md-6">
                <label class="form-label">Prefix <span class="text-danger">*</span></label>
                <select name="prefix" class="form-select form-select-lg" required>
                    <option value="">Select Prefix</option>
                    <option {{ $staff->prefix == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                    <option {{ $staff->prefix == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                    <option {{ $staff->prefix == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                    <option {{ $staff->prefix == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                    <option {{ $staff->prefix == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                    <option {{ $staff->prefix == 'Engr.' ? 'selected' : '' }}>Engr.</option>
                    <option {{ $staff->prefix == 'Rev.' ? 'selected' : '' }}>Rev.</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control form-control-lg" value="{{ $staff->first_name }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Middle Name</label>
                <input type="text" name="middlename" class="form-control form-control-lg" value="{{ $staff->middlename }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control form-control-lg" value="{{ $staff->last_name }}" required>
            </div>

            <div class="col-12">
                <label class="form-label">Role/Position <span class="text-danger">*</span></label>
                <select name="role" class="form-select form-select-lg" required>
                    <option value="">-- Select Role/Position --</option>
                    <option value="Library Coordinator" {{ $staff->role == 'Library Coordinator' ? 'selected' : '' }}>Library Coordinator</option>
                    <option value="Collections & Processing Librarian" {{ $staff->role == 'Collections & Processing Librarian' ? 'selected' : '' }}>Collections & Processing Librarian</option>
                    <option value="Reference & Users Services Assistant" {{ $staff->role == 'Reference & Users Services Assistant' ? 'selected' : '' }}>Reference & Users Services Assistant</option>
                    <option value="Collection & Processing Clerk" {{ $staff->role == 'Collection & Processing Clerk' ? 'selected' : '' }}>Collection & Processing Clerk</option>
                    <option value="AV In-Charge" {{ $staff->role == 'AV In-Charge' ? 'selected' : '' }}>AV In-Charge</option>
                    <option value="Librarian" {{ $staff->role == 'Librarian' ? 'selected' : '' }}>Librarian</option>
                    <option value="Junior High School Librarian" {{ $staff->role == 'Junior High School Librarian' ? 'selected' : '' }}>Junior High School Librarian</option>
                    <option value="Grade School Library In-Charge" {{ $staff->role == 'Grade School Library In-Charge' ? 'selected' : '' }}>Grade School Library In-Charge</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control form-control-lg" value="{{ $staff->email }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <select name="department" class="form-select form-select-lg" required>
                    <option value="">Select Department</option>
                    <option value="college" {{ $staff->department == 'college' ? 'selected' : '' }}>College Library</option>
                    <option value="graduate" {{ $staff->department == 'graduate' ? 'selected' : '' }}>Graduate Library</option>
                    <option value="senior_high" {{ $staff->department == 'senior_high' ? 'selected' : '' }}>Senior High School Library</option>
                    <option value="ibed" {{ $staff->department == 'ibed' ? 'selected' : '' }}>IBED Library</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Description of Work</label>
                <textarea name="description" class="form-control form-control-lg" rows="3">{{ $staff->description }}</textarea>
            </div>

            <div class="col-12">
                
                <input type="file" name="photo" class="form-control form-control-lg" accept="image/*">
                @if($staff->photo)
                <div class="mt-3 text-center">
                    <img src="{{ asset('storage/' . $staff->photo) }}" class="rounded-circle shadow-sm" style="width:100px; height:100px; object-fit:cover;">
                </div>
                @endif
            </div>

            <div class="col-12 d-flex justify-content-center mt-2">
                <button type="submit" class="btn btn-lg px-5 py-2" style="font-size:1.1rem; font-weight:600; background:#ffc107; color:#000; border:none; border-radius:2em;">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection