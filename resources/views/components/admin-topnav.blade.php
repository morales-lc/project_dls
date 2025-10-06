<nav id="adminTopnav" class="navbar navbar-expand-lg navbar-light sticky-top" style="background: linear-gradient(90deg, #f8bbd0 0%, #e83e8c 100%); border-bottom: 2px solid #d81b60; box-shadow: 0 4px 16px rgba(232,62,140,0.10); height:72px;">
    <div class="container-fluid px-4">
        <button id="sidebarToggleTop" class="btn btn-outline-pink d-none d-lg-inline me-3" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}" style="color:#d81b60;">
            <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38" style="object-fit:contain; border-radius:8px; background:#fff; padding:2px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <span class="d-none d-md-inline" style="font-weight:600; color:#d81b60;">Admin Panel</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2 px-3 py-2 rounded-3" type="button" id="adminProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border:1.5px solid #ffd1e3; box-shadow:0 2px 8px rgba(232,62,140,0.06);">
                    <i class="bi bi-person-circle" style="font-size:1.5rem; color:#d81b60;"></i>
                    <span class="d-none d-md-inline" style="color:#d81b60; font-weight:500;">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class="bi bi-caret-down-fill ms-1" style="font-size:0.9rem; color:#d81b60;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="adminProfileDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
