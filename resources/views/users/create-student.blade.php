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
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ccc;
    }
    .form-control:focus, .form-select:focus {
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
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        margin-top: 20px;
        color: #333;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }
</style>
@endpush

@section('title', 'Add Student/Faculty')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Add Student/Faculty</h2>
    <div class="card p-4 shadow rounded-4" style="max-width: 850px; margin:auto;">
        <form method="POST" action="{{ route('user.add') }}">
            @csrf
            <input type="hidden" name="role" value="student_faculty">

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
            <div class="section-title">Basic Information</div>
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
            </div>

            {{-- Account Information --}}
            <div class="section-title">Account Information</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role_type" id="roleTypeSelect" class="form-select" required onchange="toggleStudentFields()">
                        <option value="student" {{ old('role_type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('role_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password (optional)</label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                </div>
            </div>

            {{-- Academic Information --}}
            <div class="section-title">Academic Information</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Program</label>
                    <select name="program_id" id="programSelect" class="form-select" required>
                        <option value="">-- Select Program --</option>
                    </select>
                </div>
            </div>

            {{-- Student-Only Fields --}}
            <div id="studentFields" style="margin-top:10px;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course</label>
                        <select name="course" id="courseSelect" class="form-select">
                            <option value="">-- Select Course --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Year Level</label>
                        <input type="text" name="yrlvl" class="form-control" value="{{ old('yrlvl') }}">
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('user.management') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-pink">Add User</button>
            </div>
        </form>

        {{-- Script --}}
        <script>
            function toggleStudentFields() {
                var role = document.getElementById('roleTypeSelect').value;
                var studentFields = document.getElementById('studentFields');
                studentFields.style.display = (role === 'faculty') ? 'none' : 'block';
            }
            document.addEventListener('DOMContentLoaded', function() {
                toggleStudentFields();
                const programsEndpoint = "{{ route('api.programs') }}";
                const programSelect = document.getElementById('programSelect');
                const courseSelect = document.getElementById('courseSelect');
                programSelect.innerHTML = '<option value="">-- Select Program --</option>';

                fetch(programsEndpoint).then(r => r.json()).then(programs => {
                    programs.forEach(p => {
                        const o = document.createElement('option');
                        o.value = p.id;
                        o.textContent = p.name;
                        if (String(p.id) === String("{{ old('program_id') }}")) o.selected = true;
                        programSelect.appendChild(o);
                    });
                    if (programSelect.value) {
                        loadCourses(programSelect.value, "{{ old('course') }}");
                    }
                });

                programSelect.addEventListener('change', function() {
                    loadCourses(this.value);
                });

                function loadCourses(programId, selectedCourse = '') {
                    if (!programId) {
                        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                        return;
                    }
                    fetch('/api/programs/' + programId + '/courses').then(r => r.json()).then(courses => {
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
            });
        </script>
    </div>
</div>
@endsection
