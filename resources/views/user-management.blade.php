@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@endpush

@section('title', 'User Management')

@section('content')

<style>

</style>
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-pink">User Management</h2>
    <!-- Navigation Tabs for User Types -->
    <ul class="nav nav-pills mb-3" id="userTypeTabs">
        <li class="nav-item">
            <a class="nav-link {{ request('type', 'student_faculty') == 'student_faculty' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'student_faculty']) }}">Student/Faculty</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('type') == 'admin' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'admin']) }}">Admin</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('type') == 'librarian' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'librarian']) }}">Librarian</a>
        </li>
    </ul>
    <div class="mb-3 d-flex gap-2">
        <a class="btn btn-pink" href="{{ route('user.create', ['type' => 'student_faculty']) }}"><i class="bi bi-person-plus me-1"></i> Add Student/Faculty</a>
        <a class="btn btn-warning" href="{{ route('user.create', ['type' => 'admin']) }}"><i class="bi bi-person-plus me-1"></i> Add Admin</a>
        <a class="btn btn-success" href="{{ route('user.create', ['type' => 'librarian']) }}"><i class="bi bi-person-plus me-1"></i> Add Librarian</a>
    </div>
    <div class="card p-3 shadow rounded-4" style="max-width: 1200px; margin:auto;">
        <div class="mb-3">
            <form method="GET" action="{{ route('user.management') }}" class="row g-2 align-items-center">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="col-mb3">
                    <input type="text" name="q" class="form-control" placeholder="Search name / email / username" value="{{ $search ?? '' }}">
                </div>
                @if($type === 'student_faculty')
                <div class="col-auto">
                    <input type="text" name="school_id" class="form-control" placeholder="School ID" value="{{ $schoolId ?? '' }}">
                </div>
                @endif
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-secondary">Filter</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('user.management', ['type' => $type]) }}" class="btn btn-pink">Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white rounded-4">
                <thead class="table-pink">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        @if ($type === 'student_faculty')
                        <th>School ID</th>
                        <th>Role</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Department</th>
                        <th>Birthdate</th>
                        <th>Profile Picture</th>
                        @else
                        <th>Username</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Role</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            @if ($type === 'student_faculty')
                            {{ $user->first_name }} {{ $user->last_name }}
                            @else
                            {{ $user->name }}
                            @endif
                        </td>
                        <td>
                            @if ($type === 'student_faculty')
                            {{ $user->user->email ?? '' }}
                            @else
                            {{ $user->email }}
                            @endif
                        </td>
                        @if ($type === 'student_faculty')
                        <td>{{ $user->school_id }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->course }}</td>
                        <td>{{ $user->yrlvl }}</td>
                        <td>{{ $user->department }}</td>
                        <td>{{ $user->birthdate }}</td>
                        <td>
                            @php
                            $profilePic = $user->profile_picture;
                            $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                            @endphp
                            <img src="{{ $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name)) }}" alt="Profile Picture" class="rounded-circle" width="40" height="40">
                        </td>
                        @else
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->contact_number }}</td>
                        <td>{{ $user->address }}</td>
                        <td>{{ $user->role }}</td>
                        @endif
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">Update</button>
                            <form method="POST" action="{{ route('user.delete', $user->id) }}" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit User Modal -->
                    <!-- You may want to customize the edit modal for admin/librarian accounts -->
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Custom Modal Styles -->
    <style>
        .modal-header {
            border-bottom: none;
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 1.5rem;
        }

        .modal-title {
            font-weight: 600;
        }

        .form-label {
            font-size: 0.9rem;
            color: #555;
        }

        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            transition: 0.2s ease;
        }

        .form-control:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 0 0.2rem rgba(108, 99, 255, 0.2);
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .btn-outline-secondary {
            border-width: 1.5px;
        }

        .btn-pink {
            background-color: #e83e8c;
            border-color: #e83e8c;
            color: #fff;
        }

        .btn-pink:hover {
            background-color: #d63384;
            border-color: #d63384;
        }
    </style>

    <!-- Add pages replaced: use separate create views for Student/Faculty, Admin, Librarian -->

</div>
@endsection