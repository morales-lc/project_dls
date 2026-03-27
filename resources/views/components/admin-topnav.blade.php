<nav id="adminTopnav" class="navbar navbar-expand-lg navbar-light" style="position:fixed; top:0; left:0; right:0; z-index:1050; background: linear-gradient(90deg, #f8bbd0 0%, #e83e8c 100%); border-bottom: 2px solid #d81b60; box-shadow: 0 4px 16px rgba(232,62,140,0.10); height:72px;">
    @php
        $newLiraCount = \App\Models\LiraRequest::where(function ($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })->count();
        $newAlinetCount = \App\Models\AlinetAppointment::where(function ($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })->count();
        $totalNewRequests = $newLiraCount + $newAlinetCount;
    @endphp
    <div class="container-fluid px-4">
        <button id="sidebarToggleTop" class="btn btn-outline-pink d-inline-flex me-3" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}" style="color:#d81b60;">
            <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38" style="object-fit:contain; border-radius:8px; background:#fff; padding:2px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <span class="d-none d-md-inline" style="font-weight:600; color:#d81b60;">Admin Panel</span>
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light position-relative d-flex align-items-center justify-content-center rounded-3" type="button" id="adminNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="New requests" title="New requests" style="width:44px; height:44px; border:1.5px solid #ffd1e3; box-shadow:0 2px 8px rgba(232,62,140,0.06);">
                    <i class="bi bi-bell" style="font-size:1.25rem; color:#d81b60;"></i>
                    @if($totalNewRequests > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.7rem; min-width:1.2rem;">
                            {{ $totalNewRequests > 99 ? '99+' : $totalNewRequests }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="adminNotificationDropdown" style="min-width: 250px;">
                    <li class="px-3 py-2 small text-muted">New Requests</li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('alinet.manage', ['status' => 'pending']) }}">
                            <span><i class="bi bi-calendar-check me-2"></i>ALINET</span>
                            <span class="badge bg-danger rounded-pill">{{ $newAlinetCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('lira.manage') }}">
                            <span><i class="bi bi-journal me-2"></i>LiRA</span>
                            <span class="badge bg-danger rounded-pill">{{ $newLiraCount }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2 px-3 py-2 rounded-3" type="button" id="adminProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border:1.5px solid #ffd1e3; box-shadow:0 2px 8px rgba(232,62,140,0.06);">
                    <i class="bi bi-person-circle" style="font-size:1.5rem; color:#d81b60;"></i>
                    <span class="d-none d-md-inline" style="color:#d81b60; font-weight:500;">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class="bi bi-caret-down-fill ms-1" style="font-size:0.9rem; color:#d81b60;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="adminProfileDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.analytics') }}"><i class="bi bi-person me-2"></i>Analytics</a></li>
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
