<nav id="sidebar" class="sidebar bg-white shadow-lg border-end">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-3 py-2">
        <span class="fw-bold fs-4 text-pink">Admin</span>
        <button id="sidebarToggle" class="btn btn-outline-pink d-lg-none" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-house-door"></i> Dashboard Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('user.management') }}"><i class="bi bi-people"></i> User Management</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('libraries.staff.manage') }}"><i class="bi bi-person-badge"></i> Manage Library Staff</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('mides.management') }}"><i class="bi bi-journal-text"></i> Mides Management</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('alert-services.manage') }}"><i class="bi bi-bell"></i> Alert Services</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('alinet.manage') }}"><i class="bi bi-calendar-check"></i> ALINET Appointments</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('post.management') }}"><i class="bi bi-file-earmark-post"></i> Post Management</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('sidlak.manage') }}"><i class="bi bi-journal-richtext"></i> Sidlak Journals</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('mides.categories.panel') }}"><i class="bi bi-tags"></i> Mides Categories</a></li>
    </ul>
</nav>
