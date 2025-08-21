<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $program }} Theses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">{{ $program }} Theses</h2>
        <div>
            <a href="{{ route('mides.undergrad') }}" class="btn btn-outline-secondary me-2">&#8592; Back to Programs</a>
            <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-primary">MIDES Dashboard</a>
        </div>
    </div>
    @if($documents->count())
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Title</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->author }}</td>
                    <td>{{ $doc->year }}</td>
                    <td>{{ $doc->title }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $doc->pdf_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                        <a href="{{ asset('storage/' . $doc->pdf_path) }}" download="{{ basename($doc->pdf_path) }}" class="btn btn-outline-success btn-sm">Download</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    @else
        <div class="alert alert-info">No theses found for this program.</div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
