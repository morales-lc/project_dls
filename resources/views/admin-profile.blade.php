<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .btn-pink {
            background: #e83e8c;
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            border: none;
        }
        .btn-pink:hover {
            background: #d81b60;
        }
        .text-pink { color: #e83e8c; }
    </style>
</head>
<body class="bg-light">
@include('components.admin-topnav')
<div id="dashboardWrapper" class="d-flex position-relative">
    @include('components.admin-sidebar')
    <div class="flex-grow-1">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-6">
                    <div class="card shadow rounded-4 border-0">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <div class="mb-2">
                                    <i class="bi bi-person-circle" style="font-size:3.2rem;color:#e83e8c;"></i>
                                </div>
                                <h3 class="fw-bold text-pink mb-0">Edit Profile</h3>
                                <div class="text-muted small">Update your account information and password</div>
                            </div>
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('admin.profile.update') }}" autocomplete="off">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="name" class="form-label">Full Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="email" class="form-label">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="username" class="form-label">Username</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username', Auth::user()->username) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="contact_number" class="form-label">Contact Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                                <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number', Auth::user()->contact_number) }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="address" class="form-label">Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', Auth::user()->address) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 mt-4">
                                    <h5 class="fw-semibold text-pink mb-3"><i class="bi bi-key me-2"></i>Change Password</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                                <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="new-password">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
                                                <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-shield-check"></i></span>
                                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-pink px-4 shadow-sm">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
