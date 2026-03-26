<nav id="librarianSidebar" class="sidebar shadow-lg border-end position-fixed" style="position:fixed; top: var(--management-topnav-height, 72px); left:0; width:260px; min-width:260px; max-width:260px; height: calc(100vh - var(--management-topnav-height, 72px)); overflow-y:auto; overflow-x:hidden; overscroll-behavior: contain; touch-action: pan-y; -webkit-overflow-scrolling: touch; background:linear-gradient(135deg,#fff 80%,#ffe3f1 100%); border-radius:1.2rem 0 0 1.2rem; box-shadow:0 2px 16px rgba(232,62,140,0.08); z-index:2000; pointer-events:auto;">
    <div class="sidebar-header d-flex align-items-center justify-content-end px-4 py-3" style="background:#ffe3ef; border-bottom:1.5px solid #ffd1e3; border-radius:1.2rem 0 0 0;">
        <button id="sidebarToggleBtn" class="btn btn-outline-pink d-lg-none" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-4 px-2">
        @php
            $newLiraCount = \App\Models\LiraRequest::where(function ($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })->count();
            $newAlinetCount = \App\Models\AlinetAppointment::where(function ($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })->count();
        @endphp
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
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('mides.management') ? 'active' : '' }}" href="{{ route('mides.management') }}">
                <i class="bi bi-journal-text" style="color:#d81b60;font-size:1.3rem;"></i> <span>MIDES Management</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('catalogs.manage') || request()->routeIs('catalogs.create') || request()->routeIs('catalogs.edit') ? 'active' : '' }}" href="{{ route('catalogs.manage') }}">
                <i class="bi bi-journals" style="color:#d81b60;font-size:1.3rem;"></i> <span>Catalog Management</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('sidlak.manage') ? 'active' : '' }}" href="{{ route('sidlak.manage') }}">
                <i class="bi bi-journal-richtext" style="color:#d81b60;font-size:1.3rem;"></i> <span>SIDLAK Management</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('alinet.manage') ? 'active' : '' }}" href="{{ route('alinet.manage') }}">
                <i class="bi bi-calendar-check" style="color:#d81b60;font-size:1.3rem;"></i>
                <span>ALINET Appointments</span>
                @if($newAlinetCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $newAlinetCount > 99 ? '99+' : $newAlinetCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('alert-services.manage') ? 'active' : '' }}" href="{{ route('alert-services.manage') }}">
                <i class="bi bi-bell" style="color:#d81b60;font-size:1.3rem;"></i> <span>Alert Services</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                <i class="bi bi-bar-chart" style="color:#d81b60;font-size:1.3rem;"></i> <span>Analytics</span>
            </a>
        </li>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('e-libraries.manage') ? 'active' : '' }}" href="{{ route('e-libraries.manage') }}">
                <i class="bi bi-globe-americas" style="color:#d81b60;font-size:1.3rem;"></i> <span>Manage Online Libraries</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs('lira.manage') ? 'active' : '' }}" href="{{ route('lira.manage') }}">
                <i class="bi bi-globe-americas" style="color:#d81b60;font-size:1.3rem;"></i>
                <span>LiRA Requests</span>
                @if($newLiraCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $newLiraCount > 99 ? '99+' : $newLiraCount }}</span>
                @endif
            </a>
        </li>


    </ul>
</nav>