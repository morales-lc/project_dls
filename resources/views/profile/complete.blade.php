
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { background: #fff; min-height: 100vh; }
        .profile-title { font-size: 1.7rem; font-weight: 700; margin-top: 60px; text-align: center; color: #6c63ff; }
        .profile-box { max-width: 450px; margin: 40px auto 0 auto; border: 2px solid #6c63ff; border-radius: 10px; padding: 2.5rem 2rem 2rem 2rem; background: #fff; }
        .profile-box label { font-weight: 500; }
        .profile-box .form-control { background: #f8f9fa; }
        .profile-box .btn { background: #111; color: #fff; border-radius: 6px; font-weight: 600; }
        .profile-box .btn:hover { background: #333; }
    </style>
</head>
<body>
    <div class="profile-title">Complete Your Profile</div>
    <div class="profile-box shadow-sm">
        <h3 class="fw-bold text-center mb-4">Profile Details</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form method="POST" action="{{ url('/profile/complete') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="user_id" class="form-label">Student/Faculty ID</label>
                    <input type="text" name="school_id" id="school_id" class="form-control" value="{{ old('school_id', Auth::user()->studentFaculty->school_id ?? '') }}" required pattern="[A-Z]{1,2}[0-9]{2}-[0-9]{4}" placeholder="C22-0171">
                <div class="form-text">Format: C22-0171</div>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', Auth::user()->studentFaculty->first_name ?? (Auth::user()->name ? explode(' ', Auth::user()->name)[0] : '')) }}" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', Auth::user()->studentFaculty->last_name ?? (Auth::user()->name ? (count(explode(' ', Auth::user()->name)) > 1 ? explode(' ', Auth::user()->name)[1] : '') : '')) }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', Auth::user()->studentFaculty->email ?? Auth::user()->email) }}" readonly>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', Auth::user()->studentFaculty->username ?? Auth::user()->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" value="" placeholder="Enter new password if you want to change it">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required onchange="toggleRoleFields()">
                    <option value="">-- Select Role --</option>
                    <option value="student" {{ old('role', Auth::user()->studentFaculty->role ?? '') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="faculty" {{ old('role', Auth::user()->studentFaculty->role ?? '') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                </select>
            </div>
            <div id="studentFields" style="display: none;">
                <div class="mb-3">
                    <label for="program" class="form-label">Program</label>
                    <select id="program" class="form-select">
                        <option value="">-- Select Program --</option>
                        {{-- populated via JS --}}
                    </select>
                </div>
                <div class="mb-3">
                    <label for="course" class="form-label">Course</label>
                    <select name="course" id="course" data-current-course="{{ old('course', Auth::user()->studentFaculty->course ?? '') }}" class="form-select">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="yrlvl" class="form-label">Year Level</label>
                    <select name="yrlvl" id="yrlvl" class="form-select">
                        <option value="">-- Select Year Level --</option>
                        <option value="1" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                        <option value="Other" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            <div id="facultyFields" style="display: none;">
                <div class="mb-3">
                    <label for="program_faculty" class="form-label">Program</label>
                    <select id="program_faculty" class="form-select">
                        <option value="">-- Select Program --</option>
                        {{-- populated via JS --}}
                    </select>
                </div>
            </div>
            {{-- hidden input to hold selected program id for form submit --}}
            <input type="hidden" name="program_id" id="program_id_hidden" value="{{ old('program_id', Auth::user()->studentFaculty->program_id ?? '') }}">
            <div class="mb-3">
                <label for="birthdate" class="form-label">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" class="form-control" value="{{ old('birthdate', Auth::user()->studentFaculty->birthdate ?? '') }}" required>
            </div>
            <div class="mb-4 text-center">
                @php
                    $profilePic = Auth::user()->studentFaculty->profile_picture;
                    $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                @endphp
                <img src="{{ $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name)) }}" alt="Profile Picture" class="rounded-circle" width="80" height="80">
                <div class="mt-2">
                    <label for="profile_picture" class="form-label">Upload New Profile Picture</label>
                    <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn w-100">Save</button>
        </form>
        <script>
            function toggleRoleFields() {
                var role = document.getElementById('role').value;
                document.getElementById('studentFields').style.display = role === 'student' ? 'block' : 'none';
                document.getElementById('facultyFields').style.display = role === 'faculty' ? 'block' : 'none';
                // disable course input when faculty
                var course = document.getElementById('course');
                if (course) {
                    if (role === 'faculty') course.setAttribute('disabled', 'disabled'); else course.removeAttribute('disabled');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                toggleRoleFields();

                const programsEndpoint = "{{ route('api.programs') }}";
                const programSelect = document.getElementById('program');
                const programFacultySelect = document.getElementById('program_faculty');
                const hiddenProgram = document.getElementById('program_id_hidden');
                const currentProgram = hiddenProgram ? hiddenProgram.value : '';

                function populatePrograms() {
                    fetch(programsEndpoint).then(r => r.json()).then(programs => {
                        programs.forEach(p => {
                            const o = document.createElement('option');
                            o.value = p.id;
                            o.textContent = p.name;
                            if (String(p.id) === String(currentProgram)) o.selected = true;
                            if (programSelect) programSelect.appendChild(o.cloneNode(true));
                            if (programFacultySelect) programFacultySelect.appendChild(o.cloneNode(true));
                        });
                        if (currentProgram) loadCourses(currentProgram);
                    }).catch(err => console.error('Failed to load programs', err));
                }

                function loadCourses(programId) {
                    if (!programId) return;
                    const url = '/api/programs/' + programId + '/courses';
                    fetch(url).then(r => r.json()).then(courses => {
                        const courseSel = document.getElementById('course');
                        if (!courseSel) return;
                        courseSel.innerHTML = '<option value="">-- Select Course --</option>';
                        const currentCourse = document.getElementById('course') ? document.getElementById('course').dataset.currentCourse : '';
                        courses.forEach(c => {
                            const o = document.createElement('option');
                            o.value = c.name;
                            o.textContent = c.name;
                            if (String(c.name) === String(currentCourse)) o.selected = true;
                            courseSel.appendChild(o);
                        });
                    }).catch(err => console.error('Failed to load courses', err));
                }

                // When student program is changed
                if (programSelect) {
                    programSelect.addEventListener('change', function() {
                        const id = this.value;
                        // set hidden input for form submission
                        if (hiddenProgram) hiddenProgram.value = id;
                        // load courses
                        loadCourses(id);
                    });
                }

                // When faculty program select changed, mirror to hidden input
                if (programFacultySelect) {
                    programFacultySelect.addEventListener('change', function() {
                        if (hiddenProgram) hiddenProgram.value = this.value;
                    });
                }

                populatePrograms();
            });
        </script>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
