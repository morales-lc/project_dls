<nav id="sidebar" class="sidebar shadow-lg border-end" style="position:fixed; top: var(--management-topnav-height, 72px); left:0; width:260px; min-width:260px; max-width:260px; height: calc(100vh - var(--management-topnav-height, 72px)); overflow-y:auto; overflow-x:hidden; overscroll-behavior: contain; touch-action: pan-y; -webkit-overflow-scrolling: touch; background:linear-gradient(135deg,#fff 80%,#ffe3f1 100%); border-radius:1.2rem 0 0 1.2rem; box-shadow:0 2px 16px rgba(232,62,140,0.08); z-index:2000; pointer-events:auto;">
    <div class="sidebar-header d-flex align-items-center justify-content-end px-4 py-3" style="background:#ffe3ef; border-bottom:1.5px solid #ffd1e3; border-radius:1.2rem 0 0 0;">
        <button id="sidebarToggleBtn" class="btn btn-outline-pink d-lg-none" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-4 px-2">
        @php
        $sidebarSections = [
            'general' => [
                'label' => null,
                'items' => [
                    [ 'route' => 'admin.dashboard', 'icon' => 'bi-house-door', 'label' => 'Dashboard Home' ],
                    [ 'route' => 'user.management', 'icon' => 'bi-people', 'label' => 'User Management' ],
                ]
            ],
            'resource' => [
                'label' => 'Resource Management',
                'items' => [
                    [ 'route' => 'mides.management', 'icon' => 'bi-journal-text', 'label' => 'MIDES Management' ],
                    [ 'route' => 'sidlak.manage', 'icon' => 'bi-journal-richtext', 'label' => 'SIDLAK Journals' ],
                    [ 'route' => 'e-libraries.manage', 'icon' => 'bi-globe-americas', 'label' => 'Manage Online Libraries' ],
                    [ 'route' => 'alert-services.manage', 'icon' => 'bi-bell', 'label' => 'Alert Services' ],
                ]
            ],
            'content' => [
                'label' => 'Content Management',
                'items' => [
                    ['route' => 'library.content.manage','icon' => 'bi-pen','label' => 'Library Content' ],
                    [ 'route' => 'post.management', 'icon' => 'bi-file-earmark-post', 'label' => 'Post Management' ],
                    ['route' => 'information_literacy.manage','icon' => 'bi-book','label' => 'Information Literacy Management' ],
                ]
            ],
            'services' => [
                'label' => 'Services & Requests',
                'items' => [
                    [ 'route' => 'alinet.manage', 'icon' => 'bi-calendar-check', 'label' => 'ALINET Appointments' ],
                    [ 'route' => 'lira.manage', 'icon' => 'bi-journal', 'label' => 'Manage LIRA Requests' ],
                    [ 'route' => 'feedback.admin', 'icon' => 'bi-chat-dots', 'label' => 'Feedback' ],
                ]
            ],
            'system' => [
                'label' => 'System Management',
                'items' => [
                    [ 'route' => 'libraries.staff.manage', 'icon' => 'bi-person-badge', 'label' => 'Manage Library Staff' ],
                    [ 'route' => 'admin.analytics', 'icon' => 'bi-bar-chart', 'label' => 'Analytics' ],
                    [ 'route' => 'marc.import.form', 'icon' => 'bi-file-earmark-arrow-up', 'label' => 'Import Catalog' ],
                    [ 'route' => 'admin.backup', 'icon' => 'bi-database', 'label' => 'System Backup' ],
                ]
            ],
        ];
        @endphp
        @foreach($sidebarSections as $sectionKey => $section)
            @if($section['label'])
                <li class="nav-item mt-3 mb-2">
                    <div class="px-3 py-1">
                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ $section['label'] }}</small>
                    </div>
                </li>
            @endif
            @foreach($section['items'] as $item)
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                    <i class="bi {{ $item['icon'] }}" style="color:#d81b60;font-size:1.3rem;"></i> <span>{{ $item['label'] }}</span>
                </a>
            </li>
            @endforeach
        @endforeach
    </ul>
</nav>