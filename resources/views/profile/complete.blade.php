
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
                <input type="text" name="user_id" id="user_id" class="form-control" value="{{ old('user_id', Auth::user()->studentFaculty->user_id ?? Auth::user()->id) }}" required>
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
                    <label for="course" class="form-label">Course</label>
                    <select name="course" id="course" class="form-select">
                        <option value="">-- Select Course --</option>
                        <option value="BSIT" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                        <option value="BSBA" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                        <option value="BSED" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSED' ? 'selected' : '' }}>BSED</option>
                        <option value="BEED" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BEED' ? 'selected' : '' }}>BEED</option>
                        <option value="BSN" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSN' ? 'selected' : '' }}>BSN</option>
                        <option value="AB" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'AB' ? 'selected' : '' }}>AB</option>
                        <option value="BSA" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSA' ? 'selected' : '' }}>BSA</option>
                        <option value="BSP" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSP' ? 'selected' : '' }}>BSP</option>
                        <option value="BSHRM" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'BSHRM' ? 'selected' : '' }}>BSHRM</option>
                        <option value="Other" {{ old('course', Auth::user()->studentFaculty->course ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="yrlvl" class="form-label">Year Level</label>
                    <select name="yrlvl" id="yrlvl" class="form-select">
                        <option value="">-- Select Year Level --</option>
                        <option value="1" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == '4' ? 'selected' : '' }}>4th Year</option>
                        <option value="Other" {{ old('yrlvl', Auth::user()->studentFaculty->yrlvl ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            <div id="facultyFields" style="display: none;">
                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <select name="department" id="department" class="form-select">
                        <option value="">-- Select Department --</option>
                        <option value="IT" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'IT' ? 'selected' : '' }}>IT</option>
                        <option value="Business" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Business' ? 'selected' : '' }}>Business</option>
                        <option value="Education" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Education' ? 'selected' : '' }}>Education</option>
                        <option value="Nursing" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Nursing' ? 'selected' : '' }}>Nursing</option>
                        <option value="Arts" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Arts' ? 'selected' : '' }}>Arts</option>
                        <option value="Accountancy" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Accountancy' ? 'selected' : '' }}>Accountancy</option>
                        <option value="Psychology" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                        <option value="HRM" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'HRM' ? 'selected' : '' }}>HRM</option>
                        <option value="Other" {{ old('department', Auth::user()->studentFaculty->department ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
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
            }
            document.addEventListener('DOMContentLoaded', function() {
                toggleRoleFields();
            });
        </script>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
