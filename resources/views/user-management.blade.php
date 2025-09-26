<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div id="dashboardWrapper" class="d-flex position-relative">
        @include('components.admin-sidebar')
        <div class="flex-grow-1">
            <div class="container mt-5">
                <h2 class="mb-4">User Management</h2>
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-person-plus me-1"></i> Add User</button>
                <table class="table table-bordered table-striped">
                        <thead>
                                <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>School ID</th>
                                        <th>Role</th>
                                        <th>Course</th>
                                        <th>Year Level</th>
                                        <th>Department</th>
                                        <th>Birthdate</th>
                                        <th>Profile Picture</th>
                                        <th>Actions</th>
                                </tr>
                        </thead>
                        <tbody>
                                @foreach ($users as $user)
                                <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        <td>{{ $user->user->email ?? '' }}</td>
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
                                        <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="bi bi-pencil-square"></i></button>
                                                <form method="POST" action="{{ route('user.delete', $user->id) }}" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></button>
                                                </form>
                                        </td>
                                </tr>
                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('user.update', $user->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title" id="editUserModalLabel{{ $user->user_id }}"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">First Name</label>
                                                            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Last Name</label>
                                                            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $user->user->email ?? '' }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">School ID</label>
                                                            <input type="text" name="school_id" class="form-control" value="{{ $user->school_id }}" required pattern="[A-Z]{1,2}[0-9]{2}-[0-9]{4}" placeholder="C22-0171">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Role</label>
                                                            <input type="text" name="role" class="form-control" value="{{ $user->role }}" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Course</label>
                                                            <input type="text" name="course" class="form-control" value="{{ $user->course }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Year Level</label>
                                                            <input type="text" name="yrlvl" class="form-control" value="{{ $user->yrlvl }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Department</label>
                                                            <input type="text" name="department" class="form-control" value="{{ $user->department }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Birthdate</label>
                                                            <input type="date" name="birthdate" class="form-control" value="{{ $user->birthdate }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-white">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancel</button>
                                                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                        </tbody>
                </table>
                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('user.add') }}">
                                @csrf
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="addUserModalLabel"><i class="bi bi-person-plus me-2"></i>Add User</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body bg-light">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">First Name</label>
                                            <input type="text" name="first_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Last Name</label>
                                            <input type="text" name="last_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Email</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">School ID</label>
                                            <input type="text" name="school_id" class="form-control" required pattern="[A-Z]{1,2}[0-9]{2}-[0-9]{4}" placeholder="C22-0171">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Role</label>
                                            <input type="text" name="role" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Course</label>
                                            <input type="text" name="course" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Year Level</label>
                                            <input type="text" name="yrlvl" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Department</label>
                                            <input type="text" name="department" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Birthdate</label>
                                            <input type="date" name="birthdate" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-white">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancel</button>
                                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
