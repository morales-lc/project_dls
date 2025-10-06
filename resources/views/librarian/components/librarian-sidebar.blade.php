<nav id="librarianSidebar" class="sidebar shadow-lg border-end position-fixed" style="width:260px; min-width:260px; max-width:260px; height:100vh; overflow-y:auto; background:linear-gradient(135deg,#fff 80%,#ffe3f1 100%); border-radius:1.2rem 0 0 1.2rem; box-shadow:0 2px 16px rgba(232,62,140,0.08); z-index:1040;">
    <div class="sidebar-header d-flex align-items-center justify-content-end px-4 py-3" style="background:#ffe3ef; border-bottom:1.5px solid #ffd1e3; border-radius:1.2rem 0 0 0;">
        <button id="sidebarToggleBtn" class="btn btn-outline-pink d-lg-none" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-4 px-2">
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('librarian.dashboard') ? 'active' : '' }}" href="{{ route('librarian.dashboard') }}">
                <i class="bi bi-house-door" style="color:#d81b60;font-size:1.3rem;"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('information_literacy.*') ? 'active' : '' }}" href="{{ route('information_literacy.manage') }}">
                <i class="bi bi-book" style="color:#d81b60;font-size:1.3rem;"></i> <span>Information Literacy</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('post.management') ? 'active' : '' }}" href="{{ route('post.management') }}">
                <i class="bi bi-file-earmark-post" style="color:#d81b60;font-size:1.3rem;"></i> <span>Posts Management</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('sidlak.manage') ? 'active' : '' }}" href="{{ route('sidlak.manage') }}">
                <i class="bi bi-journal-richtext" style="color:#d81b60;font-size:1.3rem;"></i> <span>Sidlak Management</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('alinet.manage') ? 'active' : '' }}" href="{{ route('alinet.manage') }}">
                <i class="bi bi-calendar-check" style="color:#d81b60;font-size:1.3rem;"></i> <span>ALINET Appointments</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('alert-services.manage') ? 'active' : '' }}" href="{{ route('alert-services.manage') }}">
                <i class="bi bi-bell" style="color:#d81b60;font-size:1.3rem;"></i> <span>Alert Services</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('mides.management') ? 'active' : '' }}" href="{{ route('mides.management') }}">
                <i class="bi bi-journal-text" style="color:#d81b60;font-size:1.3rem;"></i> <span>MIDES Management</span>
            </a>
        </li>
    </ul>
    <div class="mt-auto py-3 px-3">
        <a href="#" id="sidebarToggleBottom" class="btn btn-outline-pink btn-sm w-100">Toggle</a>
    </div>
</nav>
