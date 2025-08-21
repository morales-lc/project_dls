<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">User Management</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Department</th>
                    <th>Birthdate</th>
                    <th>Profile Picture</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->user->email ?? '' }}</td>
                    <td>{{ $user->username }}</td>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
