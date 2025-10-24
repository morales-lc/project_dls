<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">

    <style>

        /* Logo animation */
        .nav-logo-animate:hover {
            transform: scale(1.13) rotate(-2deg);
        }

        /* Navbar link styling */
        .navbar-nav .nav-link {
            transition: transform 0.25s cubic-bezier(.4, 1.6, .6, 1), background 0.2s, color 0.2s;
            border-radius: 8px;
            letter-spacing: 0.3px;
        }

        .navbar-nav .nav-link:hover {
            transform: scale(1.05);
            background: #c2185b;
            color: #fff !important;
        }

        .navbar-nav .nav-link.active {
            background: #fff;
            color: #e83e8c !important;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(232, 62, 140, 0.08);
        }

        /* Dropdown item hover */
        .dropdown-menu .dropdown-item:hover {
            background-color: #c2185b !important;
            color: #fff !important;
        }

        /* Profile image style */
        .profile-pic {
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.2s, transform 0.2s;
            cursor: pointer;
        }

        .profile-pic:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* ===========================
   DESKTOP DROPDOWNS
   =========================== */
        @media (min-width: 992px) {
            .navbar .dropdown-menu {
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
                transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;
                margin-top: 0.4rem;
                border-radius: 10px;
                border: none;
                box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
                padding: 0.5rem 0.4rem;
                background: #fff;
            }

            /* ✅ Hover zone fix */
            .navbar .dropdown:hover::before {
                content: "";
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                height: 12px;
                background: transparent;
            }

            .navbar .dropdown:hover>.dropdown-menu,
            .navbar .dropdown-menu:hover {
                display: block;
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                pointer-events: auto;
            }

            .navbar .dropdown-menu {
                pointer-events: none;
            }

            .navbar .dropdown:hover>.dropdown-menu {
                pointer-events: auto;
            }

            .navbar .dropdown:hover>.dropdown-toggle::after {
                transform: rotate(180deg);
                transition: transform 0.2s ease;
            }
        }

        /* ===========================
   MOBILE DROPDOWNS
   =========================== */
        @media (max-width: 991.98px) {
            .navbar .dropdown-menu {
                position: static !important;
                float: none !important;
                display: none;
                margin: 0;
                padding: 0.25rem 0;
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
                width: 100%;
            }

            .navbar .dropdown.show>.dropdown-menu {
                display: block;
            }

            .navbar .dropdown .dropdown-item {
                color: #fff !important;
                padding-left: 1.1rem;
                padding-right: 1.1rem;
            }

            .navbar .dropdown .dropdown-item:hover,
            .navbar .dropdown .dropdown-item:focus {
                background: rgba(255, 255, 255, 0.06);
                color: #fff !important;
            }

            .navbar .dropdown-toggle::after {
                float: right;
                transition: transform .2s ease;
                margin-left: .5rem;
            }

            .navbar .dropdown.show>.dropdown-toggle::after {
                transform: rotate(180deg);
            }

            .navbar-collapse {
                padding-top: .5rem;
                padding-bottom: .5rem;
            }

            .navbar-nav .nav-link {
                font-size: 1rem;
                font-weight: 500;
                color: #fff !important;
                padding: 0.75rem 1rem;
                text-align: left;
                border-radius: 6px;
                transition: background 0.25s ease;
            }

            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                transform: scale(1.05);
                background: #c2185b;
                color: #fff !important;
                font-weight: 600;
                box-shadow: 0 3px 10px rgba(194, 24, 91, 0.25);
            }

            #navbarCloseBtn {
                font-size: 0.9rem;
                padding: 0.4rem 0.9rem;
                border: 1px solid rgba(255, 255, 255, 0.7);
                color: #fff;
            }

            #navbarCloseBtn i {
                font-size: 1rem;
                margin-right: 0.3rem;
            }
        }

        /* ===========================
   DROPDOWN MENU ITEMS
   =========================== */
        .dropdown-menu .dropdown-item {
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            color: #333;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }

        .dropdown-menu .dropdown-item:hover,
        .dropdown-menu .dropdown-item:focus {
            background-color: #e83e8c !important;
            color: #fff !important;
            transform: translateX(4px);
        }

        .dropdown-menu .dropdown-item.active {
            background-color: #c2185b !important;
            color: #fff !important;
        }

        /* Divider lines */
        .dropdown-menu li:not(:last-child) {
            border-bottom: 1px solid rgba(51, 51, 51, 0.08);
        }

        /* ===========================
   LOGOUT BUTTON STYLE 🔴
   =========================== */
        .dropdown-menu .dropdown-item.logout {
            background: #dc3545 !important;
            /* red */
            color: #fff !important;
            font-weight: 600;
            justify-content: center;
            border-radius: 6px;
            margin-top: 0.4rem;
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.35);
            transition: all 0.25s ease;
        }

        .dropdown-menu .dropdown-item.logout:hover {
            background: #c82333 !important;
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.4);
        }

        /* ===========================
   BRAND ANIMATION
   =========================== */
        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.35s cubic-bezier(.68, -0.55, .27, 1.55), text-shadow 0.35s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.1) translateY(-3px);
            text-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
        }

        .navbar-brand img {
            transition: box-shadow 0.35s ease;
        }

        .navbar-brand:hover img {
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
        }

        /* Bookmark badge fix (scoped to navbar to avoid leaking to page content) */
        .navbar .bg-pink {
            background-color: #e83e8c !important;
        }

        .navbar .badge.bg-pink {
            color: #fff !important;
            font-weight: 600;
            font-size: 0.8rem;
            box-shadow: 0 1px 4px rgba(232, 62, 140, 0.4);
        }

        .navbar .dropdown-item .badge {
            margin-left: auto;
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>




</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark px-3" style="background-color: #e83e8c !important;">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="{{ (Auth::check() && Auth::user()->role === 'guest') ? route('guest.dashboard') : route('dashboard') }}">
                <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38"
                    class="rounded" style="background:#fff; padding:2px;">
                <span class="d-none d-md-inline">LC MIDES Digital Library</span>
            </a>

            <!-- Toggler -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Mobile Close Button -->
                <button id="navbarCloseBtn" class="btn btn-outline-light d-lg-none ms-auto mb-2" type="button" style="border-radius:8px;">
                    <i class="bi bi-x-lg"></i> Close
                </button>
                @php $isGuest = Auth::check() && Auth::user()->role === 'guest'; @endphp
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                    @if($isGuest)
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
                    @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Home</a>
                    </li>

                    <!-- About Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('about*','feedback.form') ? 'show active' : '' }}">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('about*','feedback.form') ? 'active' : '' }}" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="{{ request()->routeIs('about*','feedback.form') ? 'true' : 'false' }}">
                            About
                        </a>
                        <ul class="dropdown-menu {{ request()->routeIs('about*','feedback.form') ? 'show' : '' }}" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Mission & Vision</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('about.contact') ? 'active' : '' }}" href="{{ route('about.contact') }}">Contact Us</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('feedback.form') ? 'active' : '' }}" href="{{ route('feedback.form') }}">Feedback</a></li>
                        </ul>
                    </li>

                    <!-- Libraries Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('libraries.*','elibraries','wiley.*','gale.*','proquest.*') ? 'show active' : '' }}">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('libraries.*','elibraries','wiley.*','gale.*','proquest.*') ? 'active' : '' }}" href="#" id="librariesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="{{ request()->routeIs('libraries.*','elibraries','wiley.*','gale.*','proquest.*') ? 'true' : 'false' }}">
                            Libraries
                        </a>
                        <ul class="dropdown-menu {{ request()->routeIs('libraries.*','elibraries','wiley.*','gale.*','proquest.*') ? 'show' : '' }}" aria-labelledby="librariesDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('libraries.ibed') ? 'active' : '' }}" href="{{ route('libraries.ibed') }}">K-10 Library</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('libraries.senior_high') ? 'active' : '' }}" href="{{ route('libraries.senior_high') }}">Senior High School Library</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('libraries.college') ? 'active' : '' }}" href="{{ route('libraries.college') }}">College Library</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('libraries.graduate') ? 'active' : '' }}" href="{{ route('libraries.graduate') }}">Graduate Library</a></li>
                            @auth
                            <li><a class="dropdown-item {{ request()->routeIs('elibraries') ? 'active' : '' }}" href="{{ route('elibraries') }}">Online E-Libraries</a></li>
                            @endauth
                        </ul>
                    </li>

                    <!-- Services Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('alert-services.*','alinet.form','learning-spaces','lira.form','information_literacy.*') ? 'show active' : '' }}">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('alert-services.*','alinet.form','learning-spaces','lira.form','information_literacy.*') ? 'active' : '' }}" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="{{ request()->routeIs('alert-services.*','alinet.form','learning-spaces','lira.form','information_literacy.*') ? 'true' : 'false' }}">
                            Services
                        </a>
                        <ul class="dropdown-menu {{ request()->routeIs('alert-services.*','alinet.form','learning-spaces','lira.form','information_literacy.*') ? 'show' : '' }}" aria-labelledby="servicesDropdown">
                            @auth
                            <li><a class="dropdown-item {{ request()->routeIs('lira.form') ? 'active' : '' }}" href="{{ route('lira.form') }}">LiRA</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('alert-services.*') ? 'active' : '' }}" href="{{ route('alert-services.index') }}">Alert Services</a></li>
                            @endauth
                            <li><a class="dropdown-item {{ request()->routeIs('alinet.form') ? 'active' : '' }}" href="{{ route('alinet.form') }}">ALINET</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('book.borrowing') ? 'active' : '' }}" href="{{ route('book.borrowing') }}">Book Borrowing</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('information_literacy.*') ? 'active' : '' }}" href="{{ route('information_literacy.index') }}">Information Literacy Alert Schedule</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('scanning.services') ? 'active' : '' }}" href="{{ route('scanning.services') }}">Scanning Services</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('learning-spaces') ? 'active' : '' }}" href="{{ route('learning-spaces') }}">Learning Spaces</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('netzone') ? 'active' : '' }}" href="{{ route('netzone') }}">Netzone</a></li>
                        </ul>
                    </li>

                    <!-- E-Resources Dropdown -->
                    @auth
                    <li class="nav-item dropdown {{ request()->routeIs('mides.*','sidlak.*') ? 'show active' : '' }}">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('mides.*','sidlak.*') ? 'active' : '' }}" href="#" id="eresourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="{{ request()->routeIs('mides.*','sidlak.*') ? 'true' : 'false' }}">
                            Electronic Resources
                        </a>
                        <ul class="dropdown-menu {{ request()->routeIs('mides.*','sidlak.*') ? 'show' : '' }}" aria-labelledby="eresourcesDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('mides.*') ? 'active' : '' }}" href="{{ route('mides.dashboard') }}">MIDES Repository</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('sidlak.*') ? 'active' : '' }}" href="{{ route('sidlak.index') }}">SIDLAK</a></li>
                        </ul>
                    </li>
                    @endauth
                    @endif
                </ul>

                <!-- Profile / Login -->
                <div class="d-flex align-items-center justify-content-center justify-content-lg-end mt-3 mt-lg-0">
                    @if(session()->has('login') || Auth::check())
                    @php
                    $profilePic = Auth::user()->studentFaculty->profile_picture ?? null;
                    $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                    $fullName = trim((Auth::user()->studentFaculty->first_name ?? '') . ' ' . (Auth::user()->studentFaculty->last_name ?? ''));
                    $isGuest = Auth::check() && Auth::user()->role === 'guest';
                    @endphp

                    @if($isGuest)
                    <!-- Guest: show only Logout button, no profile picture or dropdown -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light ms-2">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                    @else
                    <ul class="navbar-nav mb-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode($fullName ?: Auth::user()->name)) }}"
                                    alt="Profile" class="rounded-circle profile-pic" width="36" height="36" style="border:2px solid #fff; transition:box-shadow .2s; box-shadow:0 2px 8px rgba(0,0,0,0.08); cursor:pointer;">
                                <span class="fw-semibold d-none d-md-inline">{{ $fullName ?: Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person-circle me-2"></i>My Account</a></li>
                                @php
                                // Sidebar logic for bookmark count
                                $sf = Auth::user()->studentFaculty ?? null;
                                $bookmarkCount = 0;
                                if ($sf) {
                                try {
                                $bookmarkCount = \App\Models\Bookmark::where('student_faculty_id', $sf->id)->count();
                                } catch (\Throwable $e) {
                                $bookmarkCount = 0;
                                }
                                }
                                @endphp
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('bookmarks.index') }}">
                                        <i class="bi bi-bookmark-heart me-2"></i>Bookmarked Items
                                        @if($bookmarkCount > 0)
                                        <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $bookmarkCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('history') }}"><i class="bi bi-clock-history me-2"></i>Search History</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light ms-2">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarCollapse = document.getElementById('navbarSupportedContent');
            const navToggler = document.querySelector('.navbar-toggler');
            const closeBtn = document.getElementById('navbarCloseBtn');

            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(navbarCollapse, {
                toggle: false
            });

            function isMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            // Close navbar when clicking close button
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    bsCollapse.hide();
                });
            }

            // Toggle dropdowns (mobile accordion style)
            document.querySelectorAll('.navbar .dropdown').forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (!toggle) return;

                toggle.addEventListener('click', function(e) {
                    if (!isMobile()) return;
                    e.preventDefault();
                    e.stopPropagation();

                    const isOpen = dropdown.classList.contains('show');

                    // Close other dropdowns
                    document.querySelectorAll('.navbar .dropdown.show').forEach(d => {
                        if (d !== dropdown) {
                            d.classList.remove('show');
                            const t = d.querySelector('.dropdown-toggle');
                            const m = d.querySelector('.dropdown-menu');
                            if (t) t.setAttribute('aria-expanded', 'false');
                            if (m) m.classList.remove('show');
                        }
                    });

                    if (!isOpen) {
                        dropdown.classList.add('show');
                        if (menu) menu.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');

                        // ensure navbar is open if user tapped dropdown directly
                        if (!navbarCollapse.classList.contains('show')) {
                            bsCollapse.show();
                        }
                    } else {
                        dropdown.classList.remove('show');
                        if (menu) menu.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            // Close dropdowns when clicking outside navbar (mobile only)
            document.addEventListener('click', function(e) {
                if (!isMobile()) return;
                if (!e.target.closest('.navbar')) {
                    document.querySelectorAll('.navbar .dropdown.show').forEach(d => {
                        d.classList.remove('show');
                        const t = d.querySelector('.dropdown-toggle');
                        const m = d.querySelector('.dropdown-menu');
                        if (t) t.setAttribute('aria-expanded', 'false');
                        if (m) m.classList.remove('show');
                    });
                }
            });

            // Reset dropdowns when navbar is hidden
            navbarCollapse.addEventListener('hidden.bs.collapse', () => {
                document.querySelectorAll('.navbar .dropdown.show').forEach(d => {
                    d.classList.remove('show');
                    const t = d.querySelector('.dropdown-toggle');
                    const m = d.querySelector('.dropdown-menu');
                    if (t) t.setAttribute('aria-expanded', 'false');
                    if (m) m.classList.remove('show');
                });
            });

            // Reset dropdowns when resizing to desktop
            window.addEventListener('resize', () => {
                if (!isMobile()) {
                    document.querySelectorAll('.navbar .dropdown.show').forEach(d => {
                        d.classList.remove('show');
                        const t = d.querySelector('.dropdown-toggle');
                        const m = d.querySelector('.dropdown-menu');
                        if (t) t.setAttribute('aria-expanded', 'false');
                        if (m) m.classList.remove('show');
                    });
                }
            });
        });
    </script>

    <!-- Profile dropdown click redirect script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                profileDropdown.addEventListener('click', function(e) {
                    // Only redirect if user clicked the avatar or name, not dropdown items
                    if (!e.target.closest('.dropdown-menu')) {
                        window.location.href = "{{ route('profile') }}";
                    }
                });
            }
        });
    </script>



</body>

</html>