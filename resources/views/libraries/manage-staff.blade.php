<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <div id="dashboardWrapper" class="d-flex position-relative">
        @include('components.admin-sidebar')
        <div class="flex-grow-1">
            @include('navbar')
            <div class="container py-5">
                <h2 class="fw-bold mb-4 text-pink">Manage Library Staff</h2>
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <a href="{{ route('libraries.staff.create') }}" class="btn btn-pink mb-3">Add New Staff</a>
                <div class="card p-4 shadow rounded-4" style="max-width:1100px;margin:auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle bg-white rounded-4">
                            <thead class="table-pink">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Role/Position</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $s)
                                <tr>
                                    <td>
                                        <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                                    </td>
                                    <td>{{ $s->prefix }} {{ $s->first_name }} {{ $s->middlename ? $s->middlename . ' ' : '' }}{{ $s->last_name }}</td>
                                    <td>{{ $s->role }}</td>
                                    <td><a href="mailto:{{ $s->email }}" class="text-pink">{{ $s->email }}</a></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $s->department)) }}</td>
                                    <td>{{ $s->description }}</td>
                                    <td>
                                        <a href="{{ route('libraries.staff.edit', $s->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('libraries.staff.destroy', $s->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff member?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</body>
</html>


