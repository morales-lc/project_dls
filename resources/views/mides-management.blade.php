<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mides Repository Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @include('navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">MIDES Repository Management</h2>
        <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-primary">MIDES Dashboard</a>
    </div>
    <a href="{{ route('mides.upload') }}" class="btn btn-primary mb-3">Add New Document</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('mides.management') }}" class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search by title, author, year..." value="{{ $search ?? '' }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                @foreach($types as $t)
                    <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="year" {{ $sort == 'year' ? 'selected' : '' }}>Year</option>
                <option value="author" {{ $sort == 'author' ? 'selected' : '' }}>Author</option>
                <option value="title" {{ $sort == 'title' ? 'selected' : '' }}>Title</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="direction" class="form-select">
                <option value="desc" {{ $direction == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ $direction == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-dark w-100">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Category/Program</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Title</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $doc)
                <tr>
                    <td>{{ $doc->type }}</td>
                    <td>{{ $doc->category ?: $doc->program }}</td>
                    <td>{{ $doc->author }}</td>
                    <td>{{ $doc->year }}</td>
                    <td>{{ $doc->title }}</td>
                    <td><a href="{{ asset('storage/' . $doc->pdf_path) }}" target="_blank">View PDF</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $documents->links() }}
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
