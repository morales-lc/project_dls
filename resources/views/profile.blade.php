<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
  <style>
    /* Responsive tweaks */
    @media (max-width: 767.98px) {
      .card {
        margin: 0 0.5rem;
        border-radius: 1rem;
      }

      .profile-avatar {
        width: 120px !important;
        height: 120px !important;
      }

      .card-body h4 {
        font-size: 1.1rem;
      }

      .profile-row {
        padding: 0.5rem 0;
      }

      /* Make modal full-screen on very small devices for readability */
      .modal-dialog.modal-lg {
        max-width: 100%;
        margin: 0.5rem;
      }

      .modal-content {
        height: auto;
      }
    }

    .bg-pink {
      background-color: #ffd1e3 !important;
      color: #d81b60 !important;
    }

    .text-pink {
      color: #d81b60 !important;
    }

    .btn-outline-pink {
      border: 1.5px solid #ffd1e3 !important;
      color: #d81b60 !important;
      background-color: #fff !important;
      font-weight: 500;
      border-radius: 0.7rem;
      transition: 0.2s;
    }

    .btn-outline-pink:hover {
      background-color: #ffd1e3 !important;
      color: #b3134b !important;
    }

    .card-header.bg-pink {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 700;
      border-bottom: 2px solid #ffd1e3;
    }

    .card-body {
      background: linear-gradient(180deg, #fff 90%, #ffe3ef 100%);
    }

    .profile-label {
      font-weight: 600;
      color: #d81b60;
      font-size: 0.97rem;
    }

    .profile-value {
      color: #333;
      font-size: 1.05rem;
      font-weight: 500;
    }

    .profile-row {
      border-bottom: 1px solid #ffd1e3;
      padding: 0.7rem 0;
    }

    .profile-row:last-child {
      border-bottom: none;
    }

    .profile-avatar {
      border: 3px solid #ffd1e3;
      box-shadow: 0 2px 12px #ffd1e3a0;
    }
  </style>
</head>

<body>

  @include('navbar')
  <div class="d-flex">
    @include('sidebar')
    <div class="flex-grow-1 d-flex justify-content-center align-items-start py-4" style="background:#f8f9fa; min-height:80vh;">
      <div class="card shadow-lg w-100 border-0" style="max-width:980px; border-radius:1.25rem;">
        <div class="card-header bg-pink d-flex align-items-center" style="border-radius:1.25rem 1.25rem 0 0;">
          <i class="bi bi-person-circle fs-3 me-2"></i>
          <span class="fw-bold fs-5">My Profile</span>
        </div>
        <div class="card-body">
          @php
          $sf = auth()->check() ? auth()->user()->studentFaculty : null;
          @endphp

          @if(!$sf)
          <div class="alert alert-warning">Profile information not available.</div>
          @else
          <div class="row g-4">
            <div class="col-12 col-md-4 d-flex justify-content-center align-items-start">
              <div class="text-center" style="width:100%;">
                @php
                $pp = $sf->profile_picture;
                $profileSrc = null;
                if ($pp) {
                if (filter_var($pp, FILTER_VALIDATE_URL)) {
                $profileSrc = $pp;
                } else {
                // If DB has only the filename, assume it resides under storage/profile_pictures
                $relative = (strpos($pp, '/') === false) ? ('profile_pictures/' . $pp) : $pp;
                $profileSrc = asset('storage/' . ltrim($relative, '/'));
                }
                }
                @endphp
                @if(!empty($profileSrc))
                <img src="{{ $profileSrc }}" alt="{{ Auth::user()->username ?? ($sf->username ?? ($sf->first_name . ' ' . $sf->last_name)) }}" class="img-fluid rounded-circle profile-avatar" style="width:160px; height:160px; object-fit:cover; max-width:100%;">
                @else
                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center profile-avatar" style="width:160px; height:160px;">
                  <i class="bi bi-person-circle" style="font-size:4.5rem; color:#c1b7bf"></i>
                </div>
                @endif
                <div class="mt-3">
                  <button id="openEditProfile" class="btn btn-outline-pink" type="button">Edit Profile</button>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-8">
              <div class="mb-2">
                <h4 class="fw-bold mb-1 text-pink">{{ ($sf->first_name || $sf->last_name) ? ($sf->first_name . ' ' . $sf->last_name) : (Auth::user()->username ?? ($sf->username ?? 'User')) }}</h4>
                <div class="small text-muted mb-3">{{ $sf->user->email ?? '' }}</div>
              </div>
              <div class="rounded-4 shadow-sm" style="background:#fff;">
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">Username</div>
                  <div class="col-sm-6 profile-value">{{ Auth::user()->username ?? ($sf->username ?? '-') }}</div>
                </div>
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">School ID</div>
                  <div class="col-sm-6 profile-value">{{ $sf->school_id ?? '-' }}</div>
                </div>
                @if($sf->role === 'student')
                @php
                  $programName = $sf->program ? $sf->program->name : '';
                  $isJuniorHighSchool = $programName === 'Junior High School';
                @endphp
                @if(!$isJuniorHighSchool)
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">Course</div>
                  <div class="col-sm-6 profile-value">{{ $sf->course ?? '-' }}</div>
                </div>
                @endif
                @endif
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">Program</div>
                  <div class="col-sm-6 profile-value">{{ $sf->program ? $sf->program->name : ($sf->department ?? '-') }}</div>
                </div>
                @if($sf->role === 'student')
                @php
                  $programName = $sf->program ? $sf->program->name : '';
                  $isHighSchool = in_array($programName, ['Junior High School', 'Senior High School']);
                  $levelLabel = $isHighSchool ? 'Grade Level' : 'Year Level';
                @endphp
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">{{ $levelLabel }}</div>
                  <div class="col-sm-6 profile-value">{{ $sf->yrlvl ?? '-' }}</div>
                </div>
                @endif
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">Birthdate</div>
                  <div class="col-sm-6 profile-value">{{ $sf->birthdate ? date('F j, Y', strtotime($sf->birthdate)) : '-' }}</div>
                </div>
                <div class="profile-row row">
                  <div class="col-sm-6 profile-label">Member Since</div>
                  <div class="col-sm-6 profile-value">{{ $sf->created_at ? date('F j, Y', strtotime($sf->created_at)) : '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  </div>

  <!-- Edit Profile Modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editProfileForm" enctype="multipart/form-data">
          @csrf
          {{-- ensure email is submitted since it's required server-side; email is not editable here --}}
          <input type="hidden" name="email" value="{{ $sf->user->email ?? '' }}">
          <div class="modal-body">
            @if($sf)
            <div class="container-fluid">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">School ID</label>
                    <input name="school_id" id="school_id" class="form-control" value="{{ $sf->school_id ?? '' }}" title="Format: C22-0171">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input name="username" id="usernameInput" class="form-control" value="{{ Auth::user()->username ?? ($sf->username ?? '') }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">First name</label>
                    <input name="first_name" id="first_name" class="form-control" value="{{ $sf->first_name ?? '' }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Last name</label>
                    <input name="last_name" id="last_name" class="form-control" value="{{ $sf->last_name ?? '' }}">
                  </div>
                </div>
                {{-- Email is not editable here --}}
                <div class="col-md-12 mb-3">
                  <div class="small text-muted">Email: {{ $sf->user->email ?? '' }}</div>
                </div>
                @if($sf->role === 'student')
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Program</label>
                    <select name="program_id" id="programModalSelect" class="form-select" data-current-program="{{ $sf->program_id ?? '' }}">
                      <option value="">-- Select Program --</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" id="courseFieldModal">
                  <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course" id="courseInput" class="form-select" data-current-course="{{ $sf->course ?? '' }}">
                      <option value="">-- Select Course --</option>
                    </select>
                  </div>
                </div>
                @endif
                @if($sf->role === 'student')
                <div class="col-md-6">
                  <div class="mb-3">
                    @php
                      $programName = $sf->program ? $sf->program->name : '';
                      $isHighSchool = in_array($programName, ['Junior High School', 'Senior High School']);
                      $levelLabel = $isHighSchool ? 'Grade Level' : 'Year Level';
                    @endphp
                    <label class="form-label" id="yearLevelModalLabel">{{ $levelLabel }}</label>
                    <select name="yrlvl" id="yrlvlInput" class="form-select" data-current-yrlvl="{{ $sf->yrlvl ?? '' }}">
                      <option value="">-- Select {{ $levelLabel }} --</option>
                      @if($programName === 'Junior High School')
                        <option value="Grade 7" {{ ($sf->yrlvl ?? '') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                        <option value="Grade 8" {{ ($sf->yrlvl ?? '') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                        <option value="Grade 9" {{ ($sf->yrlvl ?? '') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                        <option value="Grade 10" {{ ($sf->yrlvl ?? '') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                      @elseif($programName === 'Senior High School')
                        <option value="Grade 11" {{ ($sf->yrlvl ?? '') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                        <option value="Grade 12" {{ ($sf->yrlvl ?? '') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                      @else
                        <option value="1" {{ ($sf->yrlvl ?? '') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ ($sf->yrlvl ?? '') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ ($sf->yrlvl ?? '') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ ($sf->yrlvl ?? '') == '4' ? 'selected' : '' }}>4th Year</option>
                        <option value="Other" {{ ($sf->yrlvl ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                      @endif
                    </select>
                  </div>
                </div>
                @endif
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Birthdate</label>
                    <input name="birthdate" id="birthdateInput" type="date" class="form-control" value="{{ $sf->birthdate ?? '' }}">
                  </div>
                </div>
                @if($sf->role === 'faculty')
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Program</label>
                    <select name="program_id" id="programModalSelectFaculty" class="form-select" data-current-program="{{ $sf->program_id ?? '' }}">
                      <option value="">-- Select Program --</option>
                    </select>
                  </div>
                </div>
                @endif
                <input type="hidden" name="role" id="roleInput" value="{{ $sf->role ?? '' }}">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Profile picture</label>
                    <input type="file" name="profile_picture" id="profilePictureInput" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">New password (leave blank to keep current)</label>
                    <input name="password" id="passwordInput" type="password" class="form-control" placeholder="New password">
                    <small class="text-muted d-block mt-1">
                      Must be at least 8 characters with one uppercase, lowercase, number, and special character (@$!%*?&#).
                    </small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <input name="password_confirmation" id="passwordConfirmInput" type="password" class="form-control" placeholder="Confirm new password">
                  </div>
                </div>
                <div class="col-12">
                  <div id="editErrors" class="alert alert-danger d-none"></div>
                </div>
              </div>
            </div>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-pink">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var openBtn = document.getElementById('openEditProfile');
    var editModalEl = document.getElementById('editProfileModal');
    var editModal = new bootstrap.Modal(editModalEl);

    if (openBtn) {
      openBtn.addEventListener('click', function() {
        editModal.show();
      });
    }

    var form = document.getElementById('editProfileForm');
    if (form) {
      // populate program/course selects in modal
      const programsEndpoint = "{{ route('api.programs') }}";

      function populateModalPrograms() {
        fetch(programsEndpoint).then(r => r.json()).then(programs => {
          const sel = document.getElementById('programModalSelect');
          const selF = document.getElementById('programModalSelectFaculty');
          const current = sel ? sel.dataset.currentProgram : (selF ? selF.dataset.currentProgram : '');
          programs.forEach(p => {
            // Track program names
            modalProgramNamesById[String(p.id)] = p.name;
            
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name;
            if (sel) {
              // Exclude Non-Teaching Staff from the student-facing select
              if (String(p.name).toLowerCase() !== 'non-teaching staff') {
                const o = opt.cloneNode(true);
                if (sel.dataset.currentProgram && String(p.id) === String(sel.dataset.currentProgram)) o.selected = true;
                sel.appendChild(o);
              }
            }
            if (selF) {
              const of = opt.cloneNode(true);
              if (selF.dataset.currentProgram && String(p.id) === String(selF.dataset.currentProgram)) of.selected = true;
              selF.appendChild(of);
            }
          });
          // if current program present, load courses and set label
          if (current && document.getElementById('courseInput')) {
            loadModalCourses(current);
            updateModalYearLevelLabel(current);
          }
        }).catch(err => console.error('Failed to load programs', err));
      }
      
      // Update modal year level label and options based on program
      function updateModalYearLevelLabel(programId) {
        const labelEl = document.getElementById('yearLevelModalLabel');
        const yrSel = document.getElementById('yrlvlInput');
        if (!labelEl || !yrSel) return;
        
        const programName = modalProgramNamesById[String(programId)] || '';
        const currentYrlvl = yrSel.dataset.currentYrlvl || yrSel.value || '';
        
        const buildOpt = (val, label, selected) => {
          const opt = document.createElement('option');
          opt.value = val;
          opt.textContent = label;
          if (String(val) === String(selected)) opt.selected = true;
          return opt;
        };
        
        // Clear existing options
        yrSel.innerHTML = '';
        
        if (programName === 'Junior High School') {
          labelEl.textContent = 'Grade Level';
          yrSel.appendChild(buildOpt('', '-- Select Grade Level --', ''));
          yrSel.appendChild(buildOpt('Grade 7', 'Grade 7', currentYrlvl));
          yrSel.appendChild(buildOpt('Grade 8', 'Grade 8', currentYrlvl));
          yrSel.appendChild(buildOpt('Grade 9', 'Grade 9', currentYrlvl));
          yrSel.appendChild(buildOpt('Grade 10', 'Grade 10', currentYrlvl));
        } else if (programName === 'Senior High School') {
          labelEl.textContent = 'Grade Level';
          yrSel.appendChild(buildOpt('', '-- Select Grade Level --', ''));
          yrSel.appendChild(buildOpt('Grade 11', 'Grade 11', currentYrlvl));
          yrSel.appendChild(buildOpt('Grade 12', 'Grade 12', currentYrlvl));
        } else {
          labelEl.textContent = 'Year Level';
          yrSel.appendChild(buildOpt('', '-- Select Year Level --', ''));
          yrSel.appendChild(buildOpt('1', '1st Year', currentYrlvl));
          yrSel.appendChild(buildOpt('2', '2nd Year', currentYrlvl));
          yrSel.appendChild(buildOpt('3', '3rd Year', currentYrlvl));
          yrSel.appendChild(buildOpt('4', '4th Year', currentYrlvl));
          yrSel.appendChild(buildOpt('Other', 'Other', currentYrlvl));
        }
      }

      // Track program names by ID
      var modalProgramNamesById = {};

      function loadModalCourses(programId) {
        if (!programId) return;
        
        // Check if program is Junior High School - if so, hide course field
        const programName = modalProgramNamesById[String(programId)] || '';
        const courseFieldModal = document.getElementById('courseFieldModal');
        const courseSel = document.getElementById('courseInput');
        
        if (programName === 'Junior High School') {
          if (courseFieldModal) courseFieldModal.style.display = 'none';
          if (courseSel) courseSel.innerHTML = '<option value="">-- Select Course --</option>';
          return;
        } else {
          if (courseFieldModal) courseFieldModal.style.display = 'block';
        }
        
        fetch('/api/programs/' + programId + '/courses').then(r => r.json()).then(courses => {
          if (!courseSel) return;
          courseSel.innerHTML = '<option value="">-- Select Course --</option>';
          const currentCourse = courseSel.dataset.currentCourse || '';
          courses.forEach(c => {
            const o = document.createElement('option');
            o.value = c.name;
            o.textContent = c.name;
            if (String(c.name) === String(currentCourse)) o.selected = true;
            courseSel.appendChild(o);
          });
        }).catch(err => console.error('Failed to load modal courses', err));
      }

      // wire program change in modal
      document.getElementById('programModalSelect')?.addEventListener('change', function() {
        loadModalCourses(this.value);
        updateModalYearLevelLabel(this.value);
      });
      document.getElementById('programModalSelectFaculty')?.addEventListener('change', function() {
        /* mirror if needed */ });

      populateModalPrograms();

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = form.querySelector('button[type="submit"]');
        var original = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving';

        var fd = new FormData(form);
        fetch("/profile/complete", {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: fd
        }).then(function(res) {
          if (res.status === 422) {
            return res.json().then(j => {
              throw {
                status: 422,
                body: j
              };
            });
          }
          return res.json();
        }).then(function(data) {
          if (data && data.status === 'ok') {
            // update UI: name, email, profile picture
            if (data.data) {
              var sf = data.data;
              // name
              var nameEl = document.querySelector('.card-body h4');
              if (nameEl) nameEl.textContent = (sf.first_name || sf.last_name) ? (sf.first_name + ' ' + sf.last_name) : (sf.username || 'User');
              // email
              var emailEl = document.querySelector('.card-body .small.text-muted');
              if (emailEl && sf.user && sf.user.email) emailEl.textContent = sf.user.email;
              // profile picture
              var imgWrap = document.querySelector('.col-md-4 .text-center');
              if (imgWrap) {
                var imgEl = imgWrap.querySelector('img');
                if (sf.profile_picture) {
                  var url;
                  if (/^https?:\/\//.test(sf.profile_picture)) {
                    url = sf.profile_picture;
                  } else {
                    var rel = sf.profile_picture.indexOf('/') === -1 ? ('profile_pictures/' + sf.profile_picture) : sf.profile_picture;
                    url = '/storage/' + rel.replace(/^\/+/, '');
                  }
                  if (imgEl) imgEl.src = url;
                  else {
                    imgWrap.innerHTML = '<img src="' + url + '" class="img-fluid rounded-circle" style="width:160px; height:160px; object-fit:cover;">';
                  }
                }
              }

            }
            editModal.hide();
          } else {
            alert((data && data.message) || 'Failed to update profile.');
          }
        }).catch(function(err) {
          // If validation errors, display them
          if (err && err.status === 422 && err.body && err.body.errors) {
            const container = document.getElementById('editErrors');
            container.classList.remove('d-none');
            container.innerHTML = '';
            Object.keys(err.body.errors).forEach(function(k) {
              err.body.errors[k].forEach(function(msg) {
                const p = document.createElement('div');
                p.textContent = msg;
                container.appendChild(p);
              });
            });
            // scroll modal to top to show errors
            document.querySelector('#editProfileModal .modal-body')?.scrollTo({
              top: 0,
              behavior: 'smooth'
            });
          } else {
            console.error(err);
            alert('Failed to update profile.');
          }
        }).finally(function() {
          submitBtn.disabled = false;
          submitBtn.innerHTML = original;
        });
      });
    }
  });
</script>