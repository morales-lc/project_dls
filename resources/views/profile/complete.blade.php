<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Profile</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #ffe6f2 0%, #ffb6c1 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .profile-box {
      background: #fff;
      border-radius: 20px;
      padding: 2.5rem;
      width: 100%;
      max-width: 950px;
      box-shadow: 0 10px 35px rgba(214, 51, 132, 0.25);
      animation: fadeInUp 1s ease;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(214, 51, 132, 0.3);
    }

    .profile-title {
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      color: #d63384;
      margin-bottom: 1.5rem;
      animation: fadeInDown 0.8s ease;
    }

    label {
      color: #d63384;
      font-weight: 500;
    }

    .form-control, .form-select {
      border-radius: 10px;
      border: 1.5px solid #f3c1d8;
      background-color: #fff8fb;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: #d63384;
      box-shadow: 0 0 0 0.2rem rgba(214, 51, 132, 0.25);
    }

    .btn {
      background: linear-gradient(135deg, #ff66a3, #d63384);
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 10px;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background: linear-gradient(135deg, #d63384, #b82b73);
      transform: scale(1.03);
    }

    .alert {
      border-radius: 10px;
      animation: fadeIn 0.6s ease;
    }

    .rounded-circle {
      border: 3px solid #ffb6c1;
      transition: transform 0.3s ease;
    }

    .rounded-circle:hover {
      transform: scale(1.08);
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @media (max-width: 768px) {
      .profile-box {
        padding: 2rem 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="container px-3">
    <div class="profile-title">Complete Your Profile</div>

  <div class="profile-box mx-auto">
      <h4 class="fw-bold text-center mb-4 text-pink">Profile Details</h4>

      {{-- Alerts --}}
      @if (session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Heads up:</strong> {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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

      <form method="POST" action="{{ url('/profile/complete') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label for="school_id" class="form-label">Student/Faculty ID</label>
            <input type="text" name="school_id" id="school_id" class="form-control"
              required placeholder="Enter your School ID here" value="{{ old('school_id', Auth::user()->studentFaculty->school_id ?? '') }}">
          </div>

          <div class="col-12 col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control"
              value="{{ Auth::user()->email }}" disabled>
          </div>

          <div class="col-12 col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control"
              value="{{ old('first_name', Auth::user()->studentFaculty->first_name ?? '') }}" required>
          </div>

          <div class="col-12 col-md-6">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control"
              value="{{ old('last_name', Auth::user()->studentFaculty->last_name ?? '') }}" required>
          </div>

          <div class="col-12 col-md-6">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control"
              value="{{ old('username', Auth::user()->username ?? '') }}" required>
          </div>

          <div class="col-12 col-md-6">
            <label for="birthdate" class="form-label">Birthdate</label>
            <input type="date" name="birthdate" id="birthdate" class="form-control"
              value="{{ old('birthdate', Auth::user()->studentFaculty->birthdate ?? '') }}" required>
          </div>

          <div class="col-12 col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required
              placeholder="Enter your password (min 8 characters)">
            <small class="text-muted d-block mt-1">
              <i class="bi bi-info-circle"></i> Must be at least 8 characters with:<br>
              • One uppercase letter (A-Z)<br>
              • One lowercase letter (a-z)<br>
              • One number (0-9)<br>
              • One special character (@$!%*?&#)
            </small>
          </div>

          <div class="col-12 col-md-6">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required
              placeholder="Re-enter your password">
          </div>

          <div class="col-12 col-md-6">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-select" required onchange="toggleRoleFields()">
              <option value="">-- Select Role --</option>
              <option value="student" {{ old('role', Auth::user()->studentFaculty->role ?? '') == 'student' ? 'selected' : '' }}>Student</option>
              <option value="faculty" {{ old('role', Auth::user()->studentFaculty->role ?? '') == 'faculty' ? 'selected' : '' }}>Faculty</option>
            </select>
          </div>

          <div class="col-12 col-md-6 mx-md-auto" id="studentFields" style="display: none;">
            <label for="program" class="form-label">Program</label>
            <select id="program" class="form-select">
              <option value="">-- Select Program --</option>
            </select>
          </div>

          <div class="col-12 col-md-6 mx-md-auto" id="courseField" style="display: none;">
            <label for="course" class="form-label">Course</label>
            <select name="course" id="course" class="form-select">
              <option value="">-- Select Course --</option>
            </select>
          </div>

          <div class="col-12 col-md-6 mx-md-auto" id="yearField" style="display: none;">
            <label for="yrlvl" class="form-label" id="yearLevelLabel">Year Level</label>
            <select name="yrlvl" id="yrlvl" class="form-select">
              <option value="">-- Select Year Level --</option>
              <option value="1">1st Year</option>
              <option value="2">2nd Year</option>
              <option value="3">3rd Year</option>
              <option value="4">4th Year</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="col-12 col-md-6 mx-md-auto" id="facultyFields" style="display: none;">
            <label for="program_faculty" class="form-label">Program</label>
            <select id="program_faculty" class="form-select">
              <option value="">-- Select Program --</option>
            </select>
          </div>

          <input type="hidden" name="program_id" id="program_id_hidden">

          <div class="col-12 text-center mt-4">
            @php
              $profilePic = Auth::user()->studentFaculty->profile_picture;
              $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
            @endphp
            <img src="{{ $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name)) }}" 
                 alt="Profile Picture" class="rounded-circle mb-3" width="90" height="90">
            <div>
              <label class="form-label">Upload New Profile Picture</label>
              <input type="file" name="profile_picture" id="profile_picture" class="form-control">
            </div>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn w-100 shadow">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function toggleRoleFields() {
      var role = document.getElementById('role').value;
      document.getElementById('studentFields').style.display = role === 'student' ? 'block' : 'none';
      
      // Handle course field visibility based on program selection
      const progStudent = document.getElementById('program');
      const programName = progStudent && progStudent.value ? (window.programNamesById ? window.programNamesById[String(progStudent.value)] : '') : '';
      const isJuniorHighSchool = programName === 'Junior High School';
      
      document.getElementById('courseField').style.display = (role === 'student' && !isJuniorHighSchool) ? 'block' : 'none';
      document.getElementById('yearField').style.display = role === 'student' ? 'block' : 'none';
      document.getElementById('facultyFields').style.display = role === 'faculty' ? 'block' : 'none';

      // Keep hidden program_id in sync with visible program select
      const programHidden = document.getElementById('program_id_hidden');
      if (role === 'student') {
        programHidden.value = document.getElementById('program').value || '';
      } else if (role === 'faculty') {
        programHidden.value = document.getElementById('program_faculty').value || '';
      } else {
        programHidden.value = '';
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const programsEndpoint = "{{ route('api.programs') }}";
      const courseEndpointBase = '/api/programs/';

      const roleSelect = document.getElementById('role');
      const progStudent = document.getElementById('program');
      const progFaculty = document.getElementById('program_faculty');
      const courseSelect = document.getElementById('course');
      const programHidden = document.getElementById('program_id_hidden');
  // Map of program id -> name for dynamic year-level rules
  var programNamesById = {};
  window.programNamesById = programNamesById;

      const initialRole = roleSelect.value || '';
      const initialProgramId = "{{ old('program_id', Auth::user()->studentFaculty->program_id ?? '') }}";
      const initialCourse = "{{ old('course', Auth::user()->studentFaculty->course ?? '') }}";
      const initialYr = "{{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') }}";

      function populatePrograms(selectEl, selectedId) {
        if (!selectEl) return;
        // clear
        selectEl.innerHTML = '<option value="">-- Select Program --</option>';
        fetch(programsEndpoint)
          .then(r => r.json())
          .then(list => {
            list.forEach(p => {
              // Hide Non-Teaching Staff from student program selection
              if (selectEl === progStudent && String(p.name).toLowerCase() === 'non-teaching staff') {
                // still track in map for name lookups if needed
                programNamesById[String(p.id)] = p.name;
                return;
              }
              const opt = document.createElement('option');
              opt.value = p.id;
              opt.textContent = p.name;
              if (String(p.id) === String(selectedId)) opt.selected = true;
              selectEl.appendChild(opt);
              programNamesById[String(p.id)] = p.name;
            });
            // Update hidden after population
            if (selectEl === progStudent && roleSelect.value === 'student') {
              programHidden.value = selectEl.value || '';
              // Ensure year options reflect current student program
              updateYearOptions(selectEl.value, initialYr);
            } else if (selectEl === progFaculty && roleSelect.value === 'faculty') {
              programHidden.value = selectEl.value || '';
            }
          });
      }

      function loadCourses(programId, selectedCourse = '') {
        if (!courseSelect) return;
        if (!programId) {
          courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
          return;
        }
        
        // Check if program is Junior High School - if so, hide course field
        const programName = window.programNamesById ? window.programNamesById[String(programId)] : '';
        const courseField = document.getElementById('courseField');
        if (programName === 'Junior High School') {
          if (courseField) courseField.style.display = 'none';
          courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
          return;
        } else {
          if (courseField && roleSelect.value === 'student') courseField.style.display = 'block';
        }
        
        const url = courseEndpointBase + programId + '/courses';
        fetch(url)
          .then(r => r.json())
          .then(courses => {
            courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
            courses.forEach(c => {
              const opt = document.createElement('option');
              opt.value = c.name;
              opt.textContent = c.name;
              if (String(c.name) === String(selectedCourse)) opt.selected = true;
              courseSelect.appendChild(opt);
            });
          });
      }

      // Populate both program selects so switching roles keeps data ready
      populatePrograms(progStudent, initialRole === 'student' ? initialProgramId : '');
      populatePrograms(progFaculty, initialRole === 'faculty' ? initialProgramId : '');

      // If initial role is student, load courses
      if (initialRole === 'student' && initialProgramId) {
        loadCourses(initialProgramId, initialCourse);
        // Set initial year options for the selected program
        updateYearOptions(initialProgramId, initialYr);
      }

      // Year level pre-select (just set the value; options already exist)
      if (initialYr) {
        const yrSel = document.getElementById('yrlvl');
        if (yrSel) yrSel.value = initialYr;
      }

      // Keep hidden program_id in sync
      roleSelect.addEventListener('change', function() {
        toggleRoleFields();
      });
      if (progStudent) progStudent.addEventListener('change', function() {
        if (roleSelect.value === 'student') {
          programHidden.value = this.value || '';
          loadCourses(this.value);
          updateYearOptions(this.value);
        }
      });
      if (progFaculty) progFaculty.addEventListener('change', function() {
        if (roleSelect.value === 'faculty') {
          programHidden.value = this.value || '';
        }
      });

      // Initialize visibility and hidden field
      toggleRoleFields();
    });
    // Update Year Level options based on selected program
    function updateYearOptions(programId, selectedValue = '') {
      const yrSel = document.getElementById('yrlvl');
      const yrLabel = document.getElementById('yearLevelLabel');
      if (!yrSel) return;
      const programName = (programId && typeof programId !== 'undefined') ? (window.programNamesById ? window.programNamesById[String(programId)] : null) : null;
      // Access the local map if in closure
      let name = programName;
      if (!name && typeof programNamesById !== 'undefined') {
        name = programNamesById[String(programId)] || '';
      }

      // Build options
      const buildOpt = (val, label) => `<option value="${val}">${label}</option>`;
      let html = '';
      
      if (name === 'Junior High School') {
        // Junior High School: Grade 7-10
        if (yrLabel) yrLabel.textContent = 'Grade Level';
        html = '<option value="">-- Select Grade Level --</option>';
        html += buildOpt('Grade 7', 'Grade 7');
        html += buildOpt('Grade 8', 'Grade 8');
        html += buildOpt('Grade 9', 'Grade 9');
        html += buildOpt('Grade 10', 'Grade 10');
      } else if (name === 'Senior High School') {
        // Senior High School: Grade 11-12
        if (yrLabel) yrLabel.textContent = 'Grade Level';
        html = '<option value="">-- Select Grade Level --</option>';
        html += buildOpt('Grade 11', 'Grade 11');
        html += buildOpt('Grade 12', 'Grade 12');
      } else {
        // Regular programs: Year levels
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
        // If the selectedValue doesn't exist in new options, reset to empty
        if (yrSel.value !== selectedValue) {
          yrSel.value = '';
        }
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
