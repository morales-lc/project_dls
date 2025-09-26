
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
                <h2 class="fw-bold mb-4 text-pink">Add Library Staff</h2>
                <a href="{{ route('libraries.staff.manage') }}" class="btn btn-outline-secondary mb-3">&larr; Back to Management</a>
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('libraries.staff.store') }}" enctype="multipart/form-data" class="card p-4 shadow rounded-4" style="max-width:900px;margin:auto;">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Prefix</label>
                        <select name="prefix" class="form-select" required>
                            <option value="">Select Prefix</option>
                            <option>Mr.</option>
                            <option>Ms.</option>
                            <option>Mrs.</option>
                            <option>Dr.</option>
                            <option>Prof.</option>
                            <option>Engr.</option>
                            <option>Rev.</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middlename" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role/Position</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Select Role/Position --</option>
                            <option value="Library Coordinator">Library Coordinator</option>
                            <option value="Collections & Processing Librarian">Collections & Processing Librarian</option>
                            <option value="Reference & Users Services Assistant">Reference & Users Services Assistant</option>
                            <option value="Collection & Processing Clerk">Collection & Processing Clerk</option>
                            <option value="AV In-Charge">AV In-Charge</option>
                            <option value="Librarian">Librarian</option>
                            <option value="Junior High School Librarian">Junior High School Librarian</option>
                            <option value="Grade School Library In-Charge">Grade School Library In-Charge</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select" required>
                            <option value="">Select Department</option>
                            <option value="college">College Library</option>
                            <option value="graduate">Graduate Library</option>
                            <option value="senior_high">Senior High School Library</option>
                            <option value="ibed">IBED Library</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description of Work</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo/Portrait</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-pink w-100">Add Staff</button>
                </form>
            </div>
          
        </div>
    </div>
</body>
</html>
