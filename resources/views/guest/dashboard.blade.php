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
<body class="d-flex flex-column min-vh-100" style="background-color: #f8f9fa; margin:0;">
@include('guest.navbar')
<main class="flex-grow-1">
<div class="container py-5">
    <h2 class="fw-bold mb-3">Welcome, Guest</h2>
    <p class="text-muted mb-4">You have limited access. You can browse MIDES Repository, SIDLAK Journals, E‑Libraries, and ALINET.</p>


    <div class="row g-4">
        <div class="col-md-3 col-sm-6">
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
        <div class="col-md-3 col-sm-6">
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
        <div class="col-md-3 col-sm-6">
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
        <div class="col-md-3 col-sm-6">
            <a class="card-link" href="{{ route('alinet.form') }}">
                <div class="card border-0 shadow-sm card-hover h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2" style="color:#e83e8c;">ALINET</h5>
                        <p class="text-muted">Request ALINET permits and see assistance options.</p>
                        <span class="btn btn-outline-secondary">Open ALINET</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="mt-5 small text-muted">Note: Some actions like bookmarking and downloads may require a full account.</div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@include('guest.footer')
</body>
</html>
