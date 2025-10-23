@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Edit Student/Faculty')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ request('return', route('user.management', ['type' => $sf->role ?? 'student'])) }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to User Management</a>
        <span></span>
    </div>
    <h2 class="fw-bold mb-4">Edit Student/Faculty</h2>

    <div class="card p-4 shadow rounded-4" style="max-width: 850px; margin:auto;">
        <form method="POST" action="{{ route('student_faculty.update', $sf->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="role" value="student_faculty">
            <input type="hidden" name="return_url" value="{{ request('return', url()->previous()) }}">

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Basic Information --}}
            <h5 class="fw-bold mt-3 mb-3 text-secondary">Basic Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" 
                           value="{{ old('first_name', $sf->first_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" 
                           value="{{ old('last_name', $sf->last_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="{{ old('email', $sf->user ? $sf->user->email : '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">School ID</label>
                    <input type="text" name="school_id" class="form-control" 
                           value="{{ old('school_id', $sf->school_id) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" class="form-control" 
                           value="{{ old('birthdate', $sf->birthdate) }}">
                </div>
            </div>

            {{-- Account Information --}}
            <h5 class="fw-bold mt-4 mb-3 text-secondary">Account Information</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Username (managed on user)</label>
                    <input type="text" class="form-control" value="{{ $sf->user?->username }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role_type" id="roleTypeSelect" class="form-select" 
                            required onchange="toggleStudentFields()">
                        <option value="student" {{ old('role_type', $sf->role) == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('role_type', $sf->role) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" placeholder="Managed on user" disabled>
                </div>
            </div>

            {{-- Academic Information --}}
            <h5 class="fw-bold mt-4 mb-3 text-secondary">Academic Information</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Program</label>
                    <select name="program_id" id="programSelect" class="form-select" required>
                        <option value="">-- Select Program --</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" 
                                {{ old('program_id', $sf->program_id) == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Student-Only Fields --}}
            <div id="studentFields" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Course</label>
                        <select name="course" id="courseSelect" class="form-select">
                            <option value="">-- Select Course --</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Year Level</label>
                        <input type="text" name="yrlvl" class="form-control" 
                               value="{{ old('yrlvl', $sf->yrlvl) }}">
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ request('return', route('user.management', ['type' => $sf->role ?? 'student'])) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-pink">
                    <i class="bi bi-save"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script --}}
<script>
    function toggleStudentFields() {
        var role = document.getElementById('roleTypeSelect').value;
        var studentFields = document.getElementById('studentFields');
        studentFields.style.display = (role === 'faculty') ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleStudentFields();
        const programSelect = document.getElementById('programSelect');
        const courseSelect = document.getElementById('courseSelect');

        function loadCourses(programId, selectedCourse = '') {
            if (!programId) {
                courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                return;
            }
            fetch('/api/programs/' + programId + '/courses')
                .then(r => r.json())
                .then(courses => {
                    courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                    courses.forEach(c => {
                        const o = document.createElement('option');
                        o.value = c.name;
                        o.textContent = c.name;
                        if (String(c.name) === String(selectedCourse)) o.selected = true;
                        courseSelect.appendChild(o);
                    });
                });
        }

        programSelect.addEventListener('change', function() {
            loadCourses(this.value);
        });

        // Initial load
        loadCourses(programSelect.value, "{{ old('course', $sf->course) }}");
    });
</script>
@endsection
