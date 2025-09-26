<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
</head>

<body class="bg-light">
    <div id="dashboardWrapper" class="d-flex position-relative">
        @include('components.admin-sidebar')
        <div class="flex-grow-1">
            @include('navbar')
            <div class="container py-5">
                <h2 class="fw-bold mb-4 text-pink">Edit Library Staff</h2>
                <a href="{{ route('libraries.staff.manage') }}" class="btn btn-outline-secondary mb-3">&larr; Back to Management</a>
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form method="POST" action="{{ route('libraries.staff.update', $staff->id) }}" enctype="multipart/form-data" class="card p-4 shadow rounded-4" style="max-width:900px;margin:auto;">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Prefix</label>
                        <select name="prefix" class="form-select" required>
                            <option value="">Select Prefix</option>
                            <option {{ $staff->prefix == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                            <option {{ $staff->prefix == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                            <option {{ $staff->prefix == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                            <option {{ $staff->prefix == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                            <option {{ $staff->prefix == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                            <option {{ $staff->prefix == 'Engr.' ? 'selected' : '' }}>Engr.</option>
                            <option {{ $staff->prefix == 'Rev.' ? 'selected' : '' }}>Rev.</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $staff->first_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middlename" class="form-control" value="{{ $staff->middlename }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $staff->last_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role/Position</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Select Role/Position --</option>
                            <option value="Library Coordinator" {{ $staff->role == 'Library Coordinator' ? 'selected' : '' }}>Library Coordinator</option>
                            <option value="Collections & Processing Librarian" {{ $staff->role == 'Collections & Processing Librarian' ? 'selected' : '' }}>Collections & Processing Librarian</option>
                            <option value="Reference & Users Services Assistant" {{ $staff->role == 'Reference & Users Services Assistant' ? 'selected' : '' }}>Reference & Users Services Assistant</option>
                            <option value="Collection & Processing Clerk" {{ $staff->role == 'Collection & Processing Clerk' ? 'selected' : '' }}>Collection & Processing Clerk</option>
                            <option value="AV In-Charge" {{ $staff->role == 'AV In-Charge' ? 'selected' : '' }}>AV In-Charge</option>
                            <option value="Librarian" {{ $staff->role == 'Librarian' ? 'selected' : '' }}>Librarian</option>
                            <option value="Junior High School Librarian" {{ $staff->role == 'Junior High School Librarian' ? 'selected' : '' }}>Junior High School Librarian</option>
                            <option value="Grade School Library In-Charge" {{ $staff->role == 'Grade School Library In-Charge' ? 'selected' : '' }}>Grade School Library In-Charge</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select" required>
                            <option value="">Select Department</option>
                            <option value="college" {{ $staff->department == 'college' ? 'selected' : '' }}>College Library</option>
                            <option value="graduate" {{ $staff->department == 'graduate' ? 'selected' : '' }}>Graduate Library</option>
                            <option value="senior_high" {{ $staff->department == 'senior_high' ? 'selected' : '' }}>Senior High School Library</option>
                            <option value="ibed" {{ $staff->department == 'ibed' ? 'selected' : '' }}>IBED Library</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description of Work</label>
                        <textarea name="description" class="form-control" rows="3">{{ $staff->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo/Portrait</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @if($staff->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $staff->photo) }}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                        </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-pink w-100">Update Staff</button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>