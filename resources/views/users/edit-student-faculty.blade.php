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
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $sf->user?->username) }}" required>
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
                    <label class="form-label">New Password (optional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current" autocomplete="new-password">
                    <small class="text-muted d-block mt-1">
                        Must be at least 8 characters with one uppercase, lowercase, number, and special character (@$!%*?&#).
                    </small>
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
                            @php $isNTS = strtolower($program->name) === 'non-teaching staff'; @endphp
                            <option value="{{ $program->id }}"
                                data-program-name="{{ $program->name }}"
                                {{ old('program_id', $sf->program_id) == $program->id ? 'selected' : '' }}
                                {{-- Hide NTS initially if editing a student --}}
                                @if ($isNTS && old('role_type', $sf->role) === 'student') hidden disabled @endif>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Student-Only Fields --}}
            <div id="studentFields" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-8" id="courseFieldWrapper">
                        <label class="form-label">Course</label>
                        <select name="course" id="courseSelect" class="form-select">
                            <option value="">-- Select Course --</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" id="yearLevelLabel">Year Level</label>
                        <select name="yrlvl" id="yrlvlSelect" class="form-select">
                            @php
                                $programName = $sf->program ? $sf->program->name : '';
                                $currentYrlvl = old('yrlvl', $sf->yrlvl);
                            @endphp
                            @if($programName === 'Junior High School')
                                <option value="">-- Select Grade Level --</option>
                                <option value="Grade 7" {{ $currentYrlvl == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                <option value="Grade 8" {{ $currentYrlvl == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                <option value="Grade 9" {{ $currentYrlvl == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                <option value="Grade 10" {{ $currentYrlvl == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                            @elseif($programName === 'Senior High School')
                                <option value="">-- Select Grade Level --</option>
                                <option value="Grade 11" {{ $currentYrlvl == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                <option value="Grade 12" {{ $currentYrlvl == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                            @else
                                <option value="">-- Select Year Level --</option>
                                <option value="1" {{ $currentYrlvl == '1' ? 'selected' : '' }}>1st Year</option>
                                <option value="2" {{ $currentYrlvl == '2' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3" {{ $currentYrlvl == '3' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4" {{ $currentYrlvl == '4' ? 'selected' : '' }}>4th Year</option>
                                <option value="Other" {{ $currentYrlvl == 'Other' ? 'selected' : '' }}>Other</option>
                            @endif
                        </select>
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
    var programNamesById = {};
    
    function toggleStudentFields() {
        var role = document.getElementById('roleTypeSelect').value;
        var studentFields = document.getElementById('studentFields');
        studentFields.style.display = (role === 'faculty') ? 'none' : 'block';
        
        // Check if Junior High School is selected to hide course field
        if (role === 'student') {
            var programSelect = document.getElementById('programSelect');
            var programId = programSelect ? programSelect.value : '';
            var programName = programNamesById[String(programId)] || '';
            var courseWrapper = document.getElementById('courseFieldWrapper');
            if (programName === 'Junior High School') {
                if (courseWrapper) courseWrapper.style.display = 'none';
            } else {
                if (courseWrapper) courseWrapper.style.display = 'block';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleStudentFields();
        const programSelect = document.getElementById('programSelect');
        const courseSelect = document.getElementById('courseSelect');
        const roleSelect = document.getElementById('roleTypeSelect');
        
        // Build program names map from existing options
        Array.from(programSelect.options).forEach(opt => {
            if (opt.value) {
                programNamesById[String(opt.value)] = opt.getAttribute('data-program-name') || opt.textContent;
            }
        });
        
        // Helper: toggle visibility of Non-Teaching Staff option based on role
        function toggleNTSOption() {
            const options = Array.from(programSelect.options);
            const isStudent = roleSelect && roleSelect.value === 'student';
            options.forEach(opt => {
                if (String(opt.getAttribute('data-program-name')).toLowerCase() === 'non-teaching staff') {
                    opt.hidden = isStudent;
                    opt.disabled = isStudent;
                    // If currently selected while switching to student, reset selection
                    if (isStudent && programSelect.value === opt.value) {
                        programSelect.value = '';
                        // clear courses when program cleared
                        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                    }
                }
            });
        }
        toggleNTSOption();

        function loadCourses(programId, selectedCourse = '') {
            if (!programId) {
                courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                return;
            }
            
            // Check if Junior High School - hide course field
            const programName = programNamesById[String(programId)] || '';
            const courseWrapper = document.getElementById('courseFieldWrapper');
            if (programName === 'Junior High School') {
                if (courseWrapper) courseWrapper.style.display = 'none';
                courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                return;
            } else {
                if (courseWrapper && roleSelect.value === 'student') courseWrapper.style.display = 'block';
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
        
        function updateYearLevelOptions(programId, selectedValue = '') {
            const yrSel = document.getElementById('yrlvlSelect');
            const yrLabel = document.getElementById('yearLevelLabel');
            if (!yrSel) return;
            
            const programName = programNamesById[String(programId)] || '';
            const buildOpt = (val, label) => `<option value="${val}">${label}</option>`;
            let html = '';
            
            if (programName === 'Junior High School') {
                if (yrLabel) yrLabel.textContent = 'Grade Level';
                html = '<option value="">-- Select Grade Level --</option>';
                html += buildOpt('Grade 7', 'Grade 7');
                html += buildOpt('Grade 8', 'Grade 8');
                html += buildOpt('Grade 9', 'Grade 9');
                html += buildOpt('Grade 10', 'Grade 10');
            } else if (programName === 'Senior High School') {
                if (yrLabel) yrLabel.textContent = 'Grade Level';
                html = '<option value="">-- Select Grade Level --</option>';
                html += buildOpt('Grade 11', 'Grade 11');
                html += buildOpt('Grade 12', 'Grade 12');
            } else {
                if (yrLabel) yrLabel.textContent = 'Year Level';
                html = '<option value="">-- Select Year Level --</option>';
                html += buildOpt('1', '1st Year');
                html += buildOpt('2', '2nd Year');
                html += buildOpt('3', '3rd Year');
                html += buildOpt('4', '4th Year');
                html += buildOpt('Other', 'Other');
            }
            yrSel.innerHTML = html;
            if (selectedValue) {
                yrSel.value = selectedValue;
            }
        }

        programSelect.addEventListener('change', function() {
            loadCourses(this.value);
            updateYearLevelOptions(this.value);
            toggleStudentFields();
        });
        roleSelect.addEventListener('change', function() {
            toggleStudentFields();
            toggleNTSOption();
        });

        // Initial load
        const initialProgramId = programSelect.value;
        if (initialProgramId) {
            loadCourses(initialProgramId, "{{ old('course', $sf->course) }}");
            updateYearLevelOptions(initialProgramId, "{{ old('yrlvl', $sf->yrlvl) }}");
        }
    });
</script>
@endsection
