<nav id="sidebar" class="sidebar shadow-lg border-end" style="width:260px; flex:0 0 260px; flex-shrink:0; min-height:100vh; max-height:100vh; overflow-y:auto; position:sticky; top:0; background:linear-gradient(135deg,#fff 80%,#ffe3f1 100%); border-radius:1.2rem 0 0 1.2rem; box-shadow:0 2px 16px rgba(232,62,140,0.08);">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#ffe3ef; border-bottom:1.5px solid #ffd1e3; border-radius:1.2rem 0 0 0;">
        <span class="fw-bold fs-4 text-pink d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill" style="color:#d81b60;font-size:2rem;"></i> Admin Panel
        </span>
        <button id="sidebarToggle" class="btn btn-outline-pink d-lg-none" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-4 px-2">
        @php
        $sidebarItems = [
        [ 'route' => 'admin.dashboard', 'icon' => 'bi-house-door', 'label' => 'Dashboard Home' ],
        [ 'route' => 'user.management', 'icon' => 'bi-people', 'label' => 'User Management' ],
        [ 'route' => 'libraries.staff.manage', 'icon' => 'bi-person-badge', 'label' => 'Manage Library Staff' ],
        [ 'route' => 'mides.management', 'icon' => 'bi-journal-text', 'label' => 'Mides Management' ],
        [ 'route' => 'alert-services.manage', 'icon' => 'bi-bell', 'label' => 'Alert Services' ],
        [ 'route' => 'alinet.manage', 'icon' => 'bi-calendar-check', 'label' => 'ALINET Appointments' ],
        [ 'route' => 'feedback.admin', 'icon' => 'bi-chat-dots', 'label' => 'Feedback' ],
        [ 'route' => 'post.management', 'icon' => 'bi-file-earmark-post', 'label' => 'Post Management' ],
        [ 'route' => 'sidlak.manage', 'icon' => 'bi-journal-richtext', 'label' => 'Sidlak Journals' ],
        [ 'route' => 'mides.categories.panel', 'icon' => 'bi-tags', 'label' => 'Mides Categories' ],
        [ 'route' => 'admin.contact-info', 'icon' => 'bi-telephone', 'label' => 'Contact Info Management' ],
        ];
        @endphp
        @foreach($sidebarItems as $item)
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                <i class="bi {{ $item['icon'] }}" style="color:#d81b60;font-size:1.3rem;"></i> <span>{{ $item['label'] }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</nav>