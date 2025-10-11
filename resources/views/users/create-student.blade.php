@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    body {
        background: #f8f9fa;
    }
    .card {
        border: none;
        border-radius: 16px;
        background: #fff;
    }
    .form-label {
        font-weight: 600;
        color: #555;
    }
    .form-control {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ccc;
    }
    .form-control:focus {
        border-color: #e83e8c;
        box-shadow: 0 0 4px rgba(232, 62, 140, 0.4);
    }
    .btn-pink {
        background: #e83e8c;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
    }
    .btn-outline-secondary {
        border-radius: 8px;
    }
    h2 {
        color: #e83e8c;
    }
</style>
@endpush

@section('title', 'Add Student/Faculty')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Add Student/Faculty</h2>
    <div class="card p-4 shadow rounded-4" style="max-width: 800px; margin:auto;">
        <form method="POST" action="{{ route('user.add') }}">
            @csrf
            <input type="hidden" name="role" value="student_faculty">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">School ID</label>
                    <input type="text" name="school_id" class="form-control" placeholder="C22-0171" value="{{ old('school_id') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Course</label>
                    <input type="text" name="course" class="form-control" value="{{ old('course') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year Level</label>
                    <input type="text" name="yrlvl" class="form-control" value="{{ old('yrlvl') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" value="{{ old('department') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password (optional)</label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('user.management') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-pink">Add Student/Faculty</button>
            </div>
        </form>
    </div>
</div>
@endsection
