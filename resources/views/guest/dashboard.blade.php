<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        .card-link { text-decoration: none; color: inherit; }
        .card-hover { transition: transform .2s ease, box-shadow .2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.08); }
        .btn-pink { background-color:#e83e8c; color:#fff; border:none; }
        .btn-pink:hover { background-color:#d63384; color:#fff; }
    </style>
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('guest.navbar')
<div class="container py-5">
    <h2 class="fw-bold mb-3">Welcome, Guest</h2>
    <p class="text-muted mb-4">You have limited access. You can browse MIDES Repository, SIDLAK Journals, E-Libraries, and search the catalog.</p>

    <div class="mb-4">
        <form class="d-flex flex-nowrap align-items-center gap-2 flex-wrap" method="GET" action="{{ route('catalogs.search') }}" style="max-width: 1200px;">
            <div class="input-group flex-grow-1" style="min-width: 250px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Search by keyword, title, author, ISBN, ISSN, or LCCN...">
            </div>
            <button type="submit" class="btn btn-pink">Search</button>
        </form>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a class="card-link" href="{{ route('mides.dashboard') }}">
                <div class="card border-0 shadow-sm card-hover h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2" style="color:#e83e8c;">MIDES Repository</h5>
                        <p class="text-muted">Explore Graduate, Undergraduate, Senior High research, and faculty theses.</p>
                        <span class="btn btn-outline-secondary">Open MIDES</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card-link" href="{{ route('sidlak.index') }}">
                <div class="card border-0 shadow-sm card-hover h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2" style="color:#e83e8c;">SIDLAK Journals</h5>
                        <p class="text-muted">Browse SIDLAK journal issues and articles.</p>
                        <span class="btn btn-outline-secondary">Open SIDLAK</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card-link" href="{{ route('elibraries') }}">
                <div class="card border-0 shadow-sm card-hover h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2" style="color:#e83e8c;">E-Libraries</h5>
                        <p class="text-muted">Find online databases and e-resources available for browsing.</p>
                        <span class="btn btn-outline-secondary">Open E-Libraries</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="mt-5 small text-muted">Note: Some actions like bookmarking and downloads may require a full account.</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@include('footer')
</body>
</html>
