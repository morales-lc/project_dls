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

    /* Shared animation style */
    .btn-animated {
        transition: all 0.2s ease-in-out;
    }

    /* Hover animation (slight lift + glow) */
    .btn-animated:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Click/active animation (press down effect) */
    .btn-animated:active {
        transform: scale(0.95);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
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
                <a class="btn btn-pink px-4" href="{{ $type === 'admin' ? route('staff.create', ['type' => 'admin']) : route('user.create', ['type' => 'admin']) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Admin
                </a>
            </div>

            <div id="addLibrarian" class="add-btn-wrapper {{ $type === 'librarian' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ $type === 'librarian' ? route('staff.create', ['type' => 'librarian']) : route('user.create', ['type' => 'librarian']) }}">
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
                                <div class="d-flex gap-2 justify-content-center">
                                    <!-- Update button -->
                                    <button class="btn btn-warning btn-sm px-3 btn-animated"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal{{ $user->id }}">
                                        <i class="bi bi-pencil-square me-1"></i> Update
                                    </button>

                                    <!-- Delete button -->
                                    @if (!($type === 'admin' && auth()->check() && auth()->user()->id == $user->id))
                                    <form method="POST"
                                        action="{{ $type === 'student_faculty' ? route('user.delete', $user->id) : route('staff.delete', $user->id) }}"
                                        onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm px-3 btn-animated">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserLabel{{ $user->id }}">Update User - {{ $type === 'student_faculty' ? ($user->first_name . ' ' . $user->last_name) : ($user->name ?? $user->username) }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ $type === 'student_faculty' ? route('user.update', $user->id) : route('staff.update', $user->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    @if ($type === 'student_faculty')
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $user->user->email ?? '' }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">School ID</label>
                                                            <input type="text" name="school_id" class="form-control" value="{{ $user->school_id }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Course</label>
                                                            <input type="text" name="course" class="form-control" value="{{ $user->course }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Year Level</label>
                                                            <input type="text" name="yrlvl" class="form-control" value="{{ $user->yrlvl }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Department</label>
                                                            <input type="text" name="department" class="form-control" value="{{ $user->department }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Birthdate</label>
                                                            <input type="date" name="birthdate" class="form-control" value="{{ $user->birthdate }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Role</label>
                                                            <select name="role" class="form-select">
                                                                <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                                                <option value="faculty" {{ $user->role === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Password (leave blank to keep current)</label>
                                                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Username</label>
                                                            <input type="text" name="username" class="form-control" value="{{ $user->username }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Contact Number</label>
                                                            <input type="text" name="contact_number" class="form-control" value="{{ $user->contact_number }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Address</label>
                                                            <input type="text" name="address" class="form-control" value="{{ $user->address }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Role</label>
                                                            <select name="role" class="form-select">
                                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                <option value="librarian" {{ $user->role === 'librarian' ? 'selected' : '' }}>Librarian</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Password (leave blank to keep current)</label>
                                                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-pink">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Edit Modal -->
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