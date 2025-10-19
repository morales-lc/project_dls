@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Add Student/Faculty')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 850px; background: #fff;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('user.management') }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Management</a>
            <span></span>
        </div>

        <h2 class="fw-bold mb-4 text-center" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">
            Add Student/Faculty
        </h2>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger w-100">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user.add') }}" class="row g-4">
            @csrf
            <input type="hidden" name="role" value="student_faculty">

            {{-- Basic Information --}}
            <div class="col-12">
                <h5 class="fw-bold text-secondary mt-3 mb-2">Basic Information</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control form-control-lg" value="{{ old('first_name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control form-control-lg" value="{{ old('last_name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">School ID <span class="text-danger">*</span></label>
                <input type="text" name="school_id" class="form-control form-control-lg" placeholder="C22-0171" value="{{ old('school_id') }}" required>
            </div>

            {{-- Account Information --}}
            <div class="col-12">
                <h5 class="fw-bold text-secondary mt-3 mb-2">Account Information</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label">Username (for login) <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control form-control-lg" value="{{ old('username') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" autocomplete="new-password">
            </div>

            {{-- Academic Information --}}
            <div class="col-12">
                <h5 class="fw-bold text-secondary mt-3 mb-2">Academic Information</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role_type" id="roleTypeSelect" class="form-select form-select-lg" required onchange="toggleStudentFields()">
                    <option value="student" {{ old('role_type') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="faculty" {{ old('role_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Program <span class="text-danger">*</span></label>
                <select name="program_id" id="programSelect" class="form-select form-select-lg" required>
                    <option value="">-- Select Program --</option>
                </select>
            </div>

            {{-- Student-Only Fields --}}
            <div id="studentFields" class="row g-4">
                <div class="col-md-8">
                    <label class="form-label">Course</label>
                    <select name="course" id="courseSelect" class="form-select form-select-lg">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year Level</label>
                    <input type="text" name="yrlvl" class="form-control form-control-lg" value="{{ old('yrlvl') }}">
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-12 d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-lg px-5 py-2"
                    style="font-size:1.1rem; font-weight:600; background:#d81b60; color:#fff; border:none; border-radius:2em;">
                    Add User
                </button>
            </div>
        </form>

        {{-- Script --}}
        <script>
            function toggleStudentFields() {
                var role = document.getElementById('roleTypeSelect').value;
                var studentFields = document.getElementById('studentFields');
                studentFields.style.display = (role === 'faculty') ? 'none' : 'flex';
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
