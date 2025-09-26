<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sidlak Journals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);">
<div id="dashboardWrapper" class="d-flex position-relative">
    @include('components.admin-sidebar')
    <div class="flex-grow-1">
        @include('navbar')
        <div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0 text-primary" style="letter-spacing:1px;">Manage Sidlak Journals</h2>
        <a href="{{ route('sidlak.create') }}" class="btn btn-success px-4 shadow-sm">Add Journal</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    <div class="card border-0 shadow-lg rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>ISSN</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journals as $journal)
                    <tr>
                        <td style="width: 80px;">
                            @if($journal->cover_photo)
                                <img src="{{ asset('storage/' . $journal->cover_photo) }}" alt="Cover" class="img-thumbnail rounded-3 shadow-sm" style="width:60px; height:80px; object-fit:cover; background:#fff;">
                            @else
                                <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center" style="width:60px; height:80px;">
                                    <i class="bi bi-image text-secondary fs-3"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $journal->title }}</td>
                        <td>{{ $journal->month }}</td>
                        <td>{{ $journal->year }}</td>
                        <td>{{ $journal->print_issn }}</td>
                        <td class="text-center">
                            <a href="{{ route('sidlak.edit', $journal->id) }}" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                            <form method="POST" action="{{ route('sidlak.destroy', $journal->id) }}" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this journal? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
