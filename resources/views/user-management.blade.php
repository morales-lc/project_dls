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

    /* Clickable row styles */
    .clickable-row {
        cursor: pointer;
    }

    .table-hover .clickable-row:hover {
        background-color: #fef4f8;
    }

    /* Modal detail labels */
    .detail-label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .avatar-lg {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>

<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">

        <!-- Header -->
        <h2 class="fw-bold mb-3 text-pink" style="letter-spacing: 1px; font-size: 2rem;">User Management</h2>

        <!-- Tabs -->
        <ul class="nav nav-pills mb-3" id="userTypeTabs">
            <li class="nav-item">
                <a class="nav-link {{ request('type', 'student') == 'student' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'student']) }}">Student</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'faculty' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'faculty']) }}">Faculty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'admin' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'admin']) }}">Admin</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'librarian' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'librarian']) }}">Librarian</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'guest' ? 'active' : '' }}" href="{{ route('user.management', ['type' => 'guest']) }}">Guest</a>
            </li>
        </ul>

        <!-- Add Buttons (Fade-in/out) -->
        <div class="mb-4 text-start">
            <div id="addStudentFaculty" class="add-btn-wrapper {{ in_array($type, ['student_faculty','student','faculty']) ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ route('user.create', ['type' => 'student_faculty', 'return' => request()->fullUrl()]) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Student/Faculty
                </a>
            </div>

            <div id="addAdmin" class="add-btn-wrapper {{ $type === 'admin' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ $type === 'admin' ? route('staff.create', ['type' => 'admin', 'return' => request()->fullUrl()]) : route('user.create', ['type' => 'admin', 'return' => request()->fullUrl()]) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Admin
                </a>
            </div>

            <div id="addLibrarian" class="add-btn-wrapper {{ $type === 'librarian' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ $type === 'librarian' ? route('staff.create', ['type' => 'librarian', 'return' => request()->fullUrl()]) : route('user.create', ['type' => 'librarian', 'return' => request()->fullUrl()]) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Librarian
                </a>
            </div>

            <div id="addGuest" class="add-btn-wrapper {{ $type === 'guest' ? 'show' : '' }}">
                <a class="btn btn-pink px-4" href="{{ route('user.create', ['type' => 'guest', 'return' => request()->fullUrl()]) }}">
                    <i class="bi bi-person-plus me-1"></i> Add Guest
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
                    @if($type === 'student' || $type === 'faculty')
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
                            @if ($type === 'student')
                            <th>School ID</th>
                            <th>Name</th>

                            <th>Course & Year</th>
                            @elseif ($type === 'faculty')
                            <th>School ID</th>
                            <th>Name</th>

                            <th>Program</th>
                            @else
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr class="clickable-row" data-target-id="viewUserModal{{ $user->id }}">
                            @if ($type === 'student')
                            <td>{{ $user->school_id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>

                            <td>{{ $user->course }}{{ $user->yrlvl ? ' - Yr ' . $user->yrlvl : '' }}</td>
                            @elseif ($type === 'faculty')
                            <td>{{ $user->school_id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>

                            <td>{{ $user->program ? $user->program->name : '' }}</td>
                            @else
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->contact_number }}</td>
                            <td>{{ $user->address }}</td>
                            @endif
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <!-- Row is clickable; no separate View button -->
                                    @if ($type === 'student' || $type === 'faculty')
                                        <a href="{{ route('student_faculty.edit', $user->id) }}" class="btn btn-warning btn-sm px-3 btn-animated">
                                            <i class="bi bi-pencil-square me-1"></i> Update
                                        </a>
                                    @else
                                        @if ($type === 'guest')
                                            <button class="btn btn-warning btn-sm px-3 btn-animated"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editGuestModal{{ $user->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> Update
                                            </button>
                                        @else
                                            <button class="btn btn-warning btn-sm px-3 btn-animated"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUserModal{{ $user->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> Update
                                            </button>
                                        @endif
                                    @endif

                                    <!-- Delete button -->
                                    @if (!($type === 'admin' && auth()->check() && auth()->user()->id == $user->id))
                                    <form method="POST"
                                        action="{{ in_array($type, ['student','faculty','guest']) ? route('user.delete', $user->id) : route('staff.delete', $user->id) }}"
                                        onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm px-3 btn-animated">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                <!-- View Modal for all users (enhanced) -->
                                <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="viewUserLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title d-flex align-items-center gap-2" id="viewUserLabel{{ $user->id }}">
                                                    @if ($type === 'student' || $type === 'faculty')
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                    <span class="badge text-uppercase" style="background:#ff4d84;">{{ $type }}</span>
                                                    @else
                                                    {{ $user->name }}
                                                    <span class="badge text-uppercase" style="background:#ff4d84;">{{ $user->role }}</span>
                                                    @endif
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3 align-items-start">
                                                    <div class="col-auto">
                                                        @if ($type === 'student' || $type === 'faculty')
                                                        @php
                                                        $profilePic = $user->profile_picture;
                                                        $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                                                        $avatar = $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name));
                                                        @endphp
                                                        <img src="{{ $avatar }}" alt="Avatar" class="avatar-lg shadow-sm">
                                                        @else
                                                        <div class="avatar-lg d-flex align-items-center justify-content-center bg-light border">
                                                            <i class="bi bi-person fs-1 text-secondary"></i>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="col">
                                                        @if ($type === 'student')
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="detail-label">School ID</div>
                                                                <div>{{ $user->school_id }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Email</div>
                                                                <div>{{ $user->user->email ?? '' }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Program</div>
                                                                <div>{{ $user->program ? $user->program->name : '' }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Course</div>
                                                                <div>{{ $user->course }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Year Level</div>
                                                                <div>{{ $user->yrlvl }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Birthdate</div>
                                                                <div>{{ $user->birthdate }}</div>
                                                            </div>
                                                        </div>
                                                        @elseif ($type === 'faculty')
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="detail-label">School ID</div>
                                                                <div>{{ $user->school_id }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Email</div>
                                                                <div>{{ $user->user->email ?? '' }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Program</div>
                                                                <div>{{ $user->program ? $user->program->name : '' }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Birthdate</div>
                                                                <div>{{ $user->birthdate }}</div>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Email</div>
                                                                <div>{{ $user->email }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Username</div>
                                                                <div>{{ $user->username }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Contact Number</div>
                                                                <div>{{ $user->contact_number }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Address</div>
                                                                <div>{{ $user->address }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-label">Role</div>
                                                                <div>{{ $user->role }}</div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($type === 'admin' || $type === 'librarian')
                                <!-- Edit Modal for admin/librarian only -->
                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserLabel{{ $user->id }}">Update User - {{ $user->name ?? $user->username }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('staff.update', $user->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
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
                                @endif

                                @if ($type === 'guest')
                                <!-- Edit Modal for guest accounts -->
                                <div class="modal fade" id="editGuestModal{{ $user->id }}" tabindex="-1" aria-labelledby="editGuestLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editGuestLabel{{ $user->id }}">Update Guest - {{ $user->name ?? $user->username }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('user.update', $user->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                                <input type="hidden" name="role" value="guest">
                                                <div class="modal-body">
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
                                                        <div class="col-md-6">
                                                            <label class="form-label">Password (leave blank to keep current)</label>
                                                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-pink">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Guest Edit Modal -->
                                @endif

                                
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

        // Map activeTab to the wrapper id to show
        let targetId;
        if (activeTab === 'student' || activeTab === 'faculty' || activeTab === 'student_faculty') {
            targetId = 'addStudentFaculty';
        } else if (activeTab === 'admin') {
            targetId = 'addAdmin';
        } else if (activeTab === 'librarian') {
            targetId = 'addLibrarian';
        } else if (activeTab === 'guest') {
            targetId = 'addGuest';
        }

        wrappers.forEach(el => {
            if (el.id === targetId) {
                el.classList.add('show');
            } else {
                el.classList.remove('show');
            }
        });
        // Make table rows clickable to open the corresponding view modal
        document.querySelectorAll('tr.clickable-row').forEach(row => {
            row.addEventListener('click', (e) => {
                // Ignore clicks originating inside any modal
                if (e.target.closest('.modal')) return;
                // Ignore clicks on interactive elements inside the row
                if (e.target.closest('a, button, input, textarea, select, label, form')) return;
                const id = row.getAttribute('data-target-id');
                const modalEl = document.getElementById(id);
                if (modalEl) {
                    // If already shown, do nothing to avoid stacking backdrops
                    if (modalEl.classList.contains('show')) return;
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });
        });
    });
</script>
@if ($type === 'student_faculty')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const programsEndpoint = "{{ route('api.programs') }}";
        // When a modal is shown, populate dropdowns and select values
        document.querySelectorAll('.modal').forEach(function(modal) {
            function populateProgramSelect() {
                const programSel = modal.querySelector('.program-select');
                const courseSel = modal.querySelector('.course-select');
                if (!programSel) return;
                const roleSel = modal.querySelector('.role-type-select, select[name="role_type"], #roleTypeSelect');
                const isStudent = roleSel && roleSel.value === 'student';
                // Clear previous options
                programSel.innerHTML = '<option value="">-- Select Program --</option>';
                fetch(programsEndpoint).then(r => r.json()).then(programs => {
                    const currentProgram = programSel.dataset.currentProgram;
                    programs.forEach(p => {
                        // Exclude Non-Teaching Staff when role is student
                        if (isStudent && String(p.name).toLowerCase() === 'non-teaching staff') return;
                        const o = document.createElement('option');
                        o.value = p.id;
                        o.textContent = p.name;
                        if (String(p.id) === String(currentProgram)) o.selected = true;
                        programSel.appendChild(o);
                    });
                    // If current program was filtered out, ensure none selected
                    if (!programSel.value) {
                        programSel.value = '';
                        if (courseSel) courseSel.innerHTML = '<option value="">-- Select Course --</option>';
                    }
                    // If current program, load courses
                    if (programSel.value && courseSel) {
                        loadCourses(programSel.value, courseSel, courseSel.dataset.currentCourse);
                    }
                });
                programSel.addEventListener('change', function() {
                    if (courseSel) loadCourses(this.value, courseSel);
                });
                if (roleSel) {
                    roleSel.addEventListener('change', function() {
                        populateProgramSelect();
                    }, { once: true }); // rewire on next show/change cycle
                }
            }
            modal.addEventListener('show.bs.modal', populateProgramSelect);
        });

        function loadCourses(programId, courseSel, currentCourse = '') {
            if (!programId) return;
            fetch('/api/programs/' + programId + '/courses').then(r => r.json()).then(courses => {
                courseSel.innerHTML = '<option value="">-- Select Course --</option>';
                courses.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.name;
                    o.textContent = c.name;
                    if (String(c.name) === String(currentCourse)) o.selected = true;
                    courseSel.appendChild(o);
                });
            });
        }
    });
</script>
@endif
@endsection