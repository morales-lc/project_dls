@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Add Library Staff')

@section('content')
    <div class="py-5 d-flex flex-column align-items-center justify-content-center">
        <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 800px; background: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('libraries.staff.manage') }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Management</a>
                <span></span>
            </div>
            <h2 class="fw-bold mb-4 text-center" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Add Library Staff</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

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

            <form method="POST" action="{{ route('libraries.staff.store') }}" enctype="multipart/form-data" class="row g-4">
                @csrf

                <div class="col-md-3">
                    <label class="form-label">Prefix <span class="text-danger">*</span></label>
                    <select name="prefix" class="form-select form-select-lg" required>
                        <option value="">Select Prefix</option>
                        <option {{ old('prefix') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                        <option {{ old('prefix') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                        <option {{ old('prefix') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                        <option {{ old('prefix') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                        <option {{ old('prefix') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                        <option {{ old('prefix') == 'Engr.' ? 'selected' : '' }}>Engr.</option>
                        <option {{ old('prefix') == 'Rev.' ? 'selected' : '' }}>Rev.</option>
                    </select>
                </div>

                <div class="col-md-9">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" maxlength="255" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middlename" class="form-control form-control-lg @error('middlename') is-invalid @enderror" value="{{ old('middlename') }}" maxlength="255">
                    @error('middlename')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" maxlength="255" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Role/Position <span class="text-danger">*</span></label>
                    <select name="role" class="form-select form-select-lg" required>
                        <option value="">-- Select Role/Position --</option>
                        <option value="Library Coordinator" {{ old('role') == 'Library Coordinator' ? 'selected' : '' }}>Library Coordinator</option>
                        <option value="Collections & Processing Librarian" {{ old('role') == 'Collections & Processing Librarian' ? 'selected' : '' }}>Collections & Processing Librarian</option>
                        <option value="Reference & Users Services Assistant" {{ old('role') == 'Reference & Users Services Assistant' ? 'selected' : '' }}>Reference & Users Services Assistant</option>
                        <option value="Collection & Processing Clerk" {{ old('role') == 'Collection & Processing Clerk' ? 'selected' : '' }}>Collection & Processing Clerk</option>
                        <option value="AV In-Charge" {{ old('role') == 'AV In-Charge' ? 'selected' : '' }}>AV In-Charge</option>
                        <option value="Librarian" {{ old('role') == 'Librarian' ? 'selected' : '' }}>Librarian</option>
                        <option value="Junior High School Librarian" {{ old('role') == 'Junior High School Librarian' ? 'selected' : '' }}>Junior High School Librarian</option>
                        <option value="Grade School Library In-Charge" {{ old('role') == 'Grade School Library In-Charge' ? 'selected' : '' }}>Grade School Library In-Charge</option>
                    </select>
                </div>

                <div class="col-md-7">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" maxlength="255" placeholder="example@lccdo.edu.ph" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Must be a valid @lccdo.edu.ph email address</small>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department" class="form-select form-select-lg" required>
                        <option value="">Select Department</option>
                        <option value="college" {{ old('department') == 'college' ? 'selected' : '' }}>College Library</option>
                        <option value="graduate" {{ old('department') == 'graduate' ? 'selected' : '' }}>Graduate Library</option>
                        <option value="senior_high" {{ old('department') == 'senior_high' ? 'selected' : '' }}>Senior High School Library</option>
                        <option value="ibed" {{ old('department') == 'ibed' ? 'selected' : '' }}>IBED Library</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description of Work</label>
                    <textarea name="description" class="form-control form-control-lg @error('description') is-invalid @enderror" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Maximum 1000 characters</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Photo/Portrait</label>
                    <input type="file" name="photo" class="form-control form-control-lg @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF (Max: 5MB)</small>
                </div>

                <div class="col-12 d-flex justify-content-center mt-2">
                    <button type="submit" class="btn btn-lg px-5 py-2" style="font-size:1.1rem; font-weight:600; background:#d81b60; color:#fff; border:none; border-radius:2em;">
                        Add Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
