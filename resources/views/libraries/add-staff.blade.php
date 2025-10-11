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

            <form method="POST" action="{{ route('libraries.staff.store') }}" enctype="multipart/form-data" class="row g-4">
                @csrf

                <div class="col-md-3">
                    <label class="form-label">Prefix <span class="text-danger">*</span></label>
                    <select name="prefix" class="form-select form-select-lg" required>
                        <option value="">Select Prefix</option>
                        <option>Mr.</option>
                        <option>Ms.</option>
                        <option>Mrs.</option>
                        <option>Dr.</option>
                        <option>Prof.</option>
                        <option>Engr.</option>
                        <option>Rev.</option>
                    </select>
                </div>

                <div class="col-md-9">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control form-control-lg" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middlename" class="form-control form-control-lg">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control form-control-lg" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Role/Position <span class="text-danger">*</span></label>
                    <select name="role" class="form-select form-select-lg" required>
                        <option value="">-- Select Role/Position --</option>
                        <option value="Library Coordinator">Library Coordinator</option>
                        <option value="Collections & Processing Librarian">Collections & Processing Librarian</option>
                        <option value="Reference & Users Services Assistant">Reference & Users Services Assistant</option>
                        <option value="Collection & Processing Clerk">Collection & Processing Clerk</option>
                        <option value="AV In-Charge">AV In-Charge</option>
                        <option value="Librarian">Librarian</option>
                        <option value="Junior High School Librarian">Junior High School Librarian</option>
                        <option value="Grade School Library In-Charge">Grade School Library In-Charge</option>
                    </select>
                </div>

                <div class="col-md-7">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department" class="form-select form-select-lg" required>
                        <option value="">Select Department</option>
                        <option value="college">College Library</option>
                        <option value="graduate">Graduate Library</option>
                        <option value="senior_high">Senior High School Library</option>
                        <option value="ibed">IBED Library</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description of Work</label>
                    <textarea name="description" class="form-control form-control-lg" rows="3"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Photo/Portrait</label>
                    <input type="file" name="photo" class="form-control form-control-lg" accept="image/*">
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
