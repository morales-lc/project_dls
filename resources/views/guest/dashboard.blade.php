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

    <!-- Catalog Search -->
    <div class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-pink text-white" style="font-size:0.95rem; padding:.45rem .7rem; border-radius:.65rem;"><i class="bi bi-collection me-1"></i> Catalog Search</span>
            <span class="text-muted">Search the LC MIDES library catalog</span>
        </div>

        <form class="search-bar catalog-search d-flex flex-nowrap align-items-center gap-2 flex-wrap"
            method="GET"
            action="{{ route('catalogs.search') }}"
            style="max-width: 1600px;">
            <div class="input-group flex-grow-1" style="min-width: 250px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                    placeholder="Search the Library Catalog by keyword, title, author, ISBN, ISSN, or LCCN..." aria-label="Search the library catalog" required>
            </div>
            <button type="submit"
                class="btn btn-pink search-catalog-btn"
                style="
                        background-color: #e83e8c; 
                        color: white; 
                        border: none; 
                        padding: 0.55rem 1.25rem; 
                        border-radius: 8px;
                        white-space: nowrap;
                        transition: all 0.3s ease;
                        box-shadow: 0 2px 8px rgba(232, 62, 140, 0.2);
                        ">
                <i class="bi bi-search me-1"></i>Search Catalog
            </button>
        </form>

        <style>
            @media (max-width: 575.98px) {
                .search-box .form-select,
                .search-box .btn {
                    width: 100%;
                }

                .search-box {
                    gap: .5rem;
                }
            }
        </style>
    </div>

    @php
        use Carbon\Carbon;
        $user = auth()->user();
        $daysLeft = null;
        $hoursLeft = null;
        $showWarning = false;
        
        if ($user && $user->guest_expires_at) {
            $expiresAt = Carbon::parse($user->guest_expires_at);
            $now = Carbon::now();
            
            if ($expiresAt->isFuture()) {
                // Calculate total hours remaining
                $totalHoursLeft = $now->diffInHours($expiresAt, false);
                $hoursLeft = ceil($totalHoursLeft);
                
                // Calculate days based on hours (more accurate)
                $daysLeft = floor($totalHoursLeft / 24);
                
                // Show warning if 7 days or less (168 hours or less)
                $showWarning = $totalHoursLeft <= 168;
            }
        }
    @endphp

    @if($showWarning)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            <div>
                <strong>Account Expiration Notice:</strong> Your guest account will expire 
                @if($daysLeft > 1)
                    in <strong>{{ $daysLeft }} days</strong>
                @elseif($daysLeft == 1)
                    in <strong>1 day</strong>
                @elseif($hoursLeft > 1)
                    in <strong>{{ $hoursLeft }} hours</strong>
                @else
                    <strong>very soon</strong>
                @endif
                on <strong>{{ Carbon::parse($user->guest_expires_at)->format('F j, Y \a\t g:i A') }}</strong>. 
                Please submit a new ALINET request if you need continued access.
            </div>
        </div>
    @endif

    

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>

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

    <!-- <div class="mt-5 small text-muted">Note: Some actions like bookmarking and downloads may require a full account.</div> -->
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@include('guest.footer')
</body>
</html>
