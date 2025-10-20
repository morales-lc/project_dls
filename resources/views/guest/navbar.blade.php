<nav class="navbar navbar-expand-lg navbar-dark px-3" style="background-color: #e83e8c !important;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="{{ route('guest.dashboard') }}">
            <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38" class="rounded" style="background:#fff; padding:2px;">
            <span class="d-none d-md-inline">LC MIDES Digital Library</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar" aria-controls="guestNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="guestNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guest.dashboard') ? 'active' : '' }}" href="{{ route('guest.dashboard') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mides.*') ? 'active' : '' }}" href="{{ route('mides.dashboard') }}">MIDES</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sidlak.*') ? 'active' : '' }}" href="{{ route('sidlak.index') }}">SIDLAK</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('elibraries') ? 'active' : '' }}" href="{{ route('elibraries') }}">E‑Libraries</a>
                </li>
            </ul>

            <div class="d-flex">
                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </div>
    <style>
        .navbar-nav .nav-link { transition: transform 0.25s cubic-bezier(.4, 1.6, .6, 1), background 0.2s, color 0.2s; border-radius: 8px; }
        .navbar-nav .nav-link:hover { transform: scale(1.05); background: #c2185b; color: #fff !important; }
        .navbar-nav .nav-link.active { background: #fff; color: #e83e8c !important; font-weight: bold; box-shadow: 0 2px 8px rgba(232, 62, 140, 0.08); }
    </style>
</nav>