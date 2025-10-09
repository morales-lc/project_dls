@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'User Management')

@section('content')
<style>
/* Smooth fade animation for Add buttons */
.add-btn-wrapper {
    opacity: 0;
    transform: translateY(-5px);
    transition: opacity 0.4s ease, transform 0.4s ease;
    display: none;
}
.add-btn-wrapper.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}
</style>

<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        
        <!-- Header -->
        <h2 class="fw-bold mb-3 text-pink" style="letter-spacing: 1px; font-size: 2rem;">User Management</h2>

        <!-- Tabs -->
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

        <!-- Add Buttons (Fade-in/out) -->
        <div class="mb-4 text-start">
            <div id="addStudentFaculty" class="add-btn-wrapper {{ $type === 'student_faculty' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ route('user.create', ['type' => 'student_faculty']) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Student/Faculty
                </a>
            </div>

            <div id="addAdmin" class="add-btn-wrapper {{ $type === 'admin' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ route('user.create', ['type' => 'admin']) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Admin
                </a>
            </div>

            <div id="addLibrarian" class="add-btn-wrapper {{ $type === 'librarian' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ route('user.create', ['type' => 'librarian']) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Librarian
                </a>
            </div>
        </div>

        <!-- Table + Filter -->
        <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="mb-3">
                <form method="GET" action="{{ route('user.management') }}" class="row g-2 align-items-center">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="col">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth fade effect when switching tabs
document.addEventListener('DOMContentLoaded', () => {
    const wrappers = document.querySelectorAll('.add-btn-wrapper');
    const activeTab = '{{ $type }}';

    wrappers.forEach(el => {
        if (el.id === `add${activeTab.charAt(0).toUpperCase() + activeTab.slice(1).replace('_faculty', 'Faculty')}`) {
            el.classList.add('show');
        } else {
            el.classList.remove('show');
        }
    });
});
</script>
@endsection
