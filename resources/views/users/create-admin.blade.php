@extends('layouts.management')

@push('management-head')
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
        border-color: #ffc107;
        box-shadow: 0 0 4px rgba(255, 193, 7, 0.4);
    }
    .btn-warning {
        background: #ffc107;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
    }
    .btn-outline-secondary {
        border-radius: 8px;
    }
    h2 {
        color: #ffc107;
    }
</style>
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Add Admin')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Add Admin</h2>
    <div class="card p-4 shadow rounded-4" style="max-width: 700px; margin:auto;">
        <form method="POST" action="{{ route('user.add') }}">
            @csrf
            <input type="hidden" name="role" value="admin">

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
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('user.management') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-warning">Add Admin</button>
            </div>
        </form>
    </div>
</div>
@endsection
