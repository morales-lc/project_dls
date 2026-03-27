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

            $sidebarSections = [
                'general' => [
                    'label' => null,
                    'items' => [
                        [
                            'route' => 'librarian.dashboard',
                            'icon' => 'bi-house-door',
                            'label' => 'Dashboard',
                        ],
                    ],
                ],
                'resource' => [
                    'label' => 'Resource Management',
                    'items' => [
                        [
                            'route' => 'mides.management',
                            'icon' => 'bi-journal-text',
                            'label' => 'MIDES Management',
                            'active' => ['mides.management', 'mides.upload'],
                        ],
                        [
                            'route' => 'catalogs.manage',
                            'icon' => 'bi-journals',
                            'label' => 'Catalog Management',
                            'active' => ['catalogs.manage', 'catalogs.create', 'catalogs.edit'],
                        ],
                        [
                            'route' => 'yearbook.manage',
                            'icon' => 'bi-collection-play',
                            'label' => 'Yearbook Archive',
                            'active' => ['yearbook.manage', 'yearbook.create', 'yearbook.edit'],
                        ],
                        [
                            'route' => 'sidlak.manage',
                            'icon' => 'bi-journal-richtext',
                            'label' => 'SIDLAK Management',
                        ],
                        [
                            'route' => 'e-libraries.manage',
                            'icon' => 'bi-globe-americas',
                            'label' => 'Manage Online Libraries',
                        ],
                        [
                            'route' => 'alert-services.manage',
                            'icon' => 'bi-bell',
                            'label' => 'Alert Services',
                        ],
                    ],
                ],
                'content' => [
                    'label' => 'Content Management',
                    'items' => [
                        [
                            'route' => 'library.content.manage',
                            'icon' => 'bi-layout-text-window-reverse',
                            'label' => 'Library Content Management',
                            'active' => ['library.content.*'],
                        ],
                        [
                            'route' => 'post.management',
                            'icon' => 'bi-file-earmark-post',
                            'label' => 'Posts Management',
                            'active' => ['post.management', 'post.create', 'post.edit'],
                        ],
                        [
                            'route' => 'information_literacy.manage',
                            'icon' => 'bi-book',
                            'label' => 'Information Literacy',
                            'active' => ['information_literacy.*'],
                        ],
                    ],
                ],
                'services' => [
                    'label' => 'Services & Requests',
                    'items' => [
                        [
                            'route' => 'alinet.manage',
                            'icon' => 'bi-calendar-check',
                            'label' => 'ALINET Appointments',
                            'badge' => 'alinet',
                        ],
                        [
                            'route' => 'lira.manage',
                            'icon' => 'bi-journal',
                            'label' => 'LiRA Requests',
                            'badge' => 'lira',
                        ],
                    ],
                ],
                'system' => [
                    'label' => 'System Management',
                    'items' => [
                        [
                            'route' => 'admin.analytics',
                            'icon' => 'bi-bar-chart',
                            'label' => 'Analytics',
                        ],
                    ],
                ],
            ];
        @endphp

        @foreach($sidebarSections as $section)
            @if($section['label'])
                <li class="nav-item mt-3 mb-2">
                    <div class="px-3 py-1">
                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ $section['label'] }}</small>
                    </div>
                </li>
            @endif

            @foreach($section['items'] as $item)
                @php
                    $activePatterns = $item['active'] ?? [$item['route']];
                    $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                @endphp
                <li class="nav-item mb-1">
                    <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ $isActive ? 'active' : '' }}" href="{{ route($item['route']) }}">
                        <i class="bi {{ $item['icon'] }}" style="color:#d81b60;font-size:1.3rem;"></i>
                        <span>{{ $item['label'] }}</span>

                        @if(($item['badge'] ?? null) === 'alinet' && $newAlinetCount > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $newAlinetCount > 99 ? '99+' : $newAlinetCount }}</span>
                        @endif

                        @if(($item['badge'] ?? null) === 'lira' && $newLiraCount > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $newLiraCount > 99 ? '99+' : $newLiraCount }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        @endforeach
    </ul>
</nav>