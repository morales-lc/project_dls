<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark bg-pink px-4" style="background-color: #e83e8c !important;">
        <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="nav-logo-animate d-flex align-items-center gap-2" style="transition:transform 0.35s cubic-bezier(.4,1.6,.6,1);">
                <img src="{{ asset('images/learningcommons.png') }}" alt="Lourdes College Library Logo" width="38" height="38" style="object-fit:contain; border-radius:8px; background:#fff; padding:2px; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition:transform 0.35s cubic-bezier(.4,1.6,.6,1);">
                <span class="d-none d-md-inline nav-logo-text" style="transition:transform 0.35s cubic-bezier(.4,1.6,.6,1);">LC MIDES Digital Library</span>
            </span>
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('about') ? 'active' : '' }}" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        About
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Mission & Vision</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('about.contact') ? 'active' : '' }}" href="{{ route('about.contact') }}">Contact Us</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('feedback.form') ? 'active' : '' }}" href="{{ route('feedback.form') }}"> Feedback</a></li>
                        
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('wiley.*','gale.*','proquest.*') ? 'active' : '' }}" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Libraries
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="{{ route('libraries.college') }}">College Library</a></li>
                        <li><a class="dropdown-item" href="{{ route('libraries.graduate') }}">Graduate Library</a></li>
                        <li><a class="dropdown-item" href="{{ route('libraries.senior_high') }}">Senior High School Library</a></li>
                        <li><a class="dropdown-item" href="{{ route('libraries.ibed') }}">IBED Library</a></li>
                        <li><a class="dropdown-item" href="{{ route('elibraries') }}">Online E-Libraries</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('alert-services.*','alinet.form','learning-spaces') ? 'active' : '' }}" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="{{ route('lira.form') }}">LiRA</a></li>
                        <li><a class="dropdown-item" href="{{ route('alert-services.index') }}">Alert Services</a></li>
                        <li><a class="dropdown-item" href="{{ route('alinet.form') }}">ALINET</a></li>
                        <li><a class="dropdown-item" href="#">Book borrowing</a></li>
                        <li><a class="dropdown-item" href="#">Information Literacy Alert Schedule</a></li>
                        <li><a class="dropdown-item" href="#">Scanning Services</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('learning-spaces') ? 'active' : '' }}" href="{{ route('learning-spaces') }}">Learning Spaces</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('mides.dashboard','sidlak.index') ? 'active' : '' }}" href="#" id="eresourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Electronic Resources
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="eresourcesDropdown">
                        <li><a class="dropdown-item" href="{{ route('mides.dashboard') }}">MIDES repository</a></li>
                        <li><a class="dropdown-item" href="{{ route('sidlak.index') }}">SIDLAk</a></li>
                    </ul>
                </li>
                    <ul class="dropdown-menu" aria-labelledby="eresourcesDropdown">
                        <li><a class="dropdown-item" href="{{ route('mides.dashboard') }}">MIDES repository</a></li>
                        <li><a class="dropdown-item" href="{{ route('sidlak.index') }}">SIDLAk</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white">
                @if(session()->has('login') || Auth::check())
                    @php
                    $profilePic = Auth::user()->studentFaculty->profile_picture ?? null;
                    $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                    $fullName = trim((Auth::user()->studentFaculty->first_name ?? '') . ' ' . (Auth::user()->studentFaculty->last_name ?? ''));
                    @endphp
                    <a href="{{ route('profile') }}">
                        <img src="{{ $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode($fullName ?: Auth::user()->name)) }}" alt="Profile Picture" class="rounded-circle me-2" width="36" height="36" style="border:2px solid #fff; transition:box-shadow .2s; box-shadow:0 2px 8px rgba(0,0,0,0.08); cursor:pointer;">
                    </a>
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">{{ $fullName ?: Auth::user()->name }}</span>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-white p-0 m-0 align-baseline" style="text-decoration:underline;">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light ms-2">Login</a>
                @endif
            </div>
        </div>
    </nav>

    





    <!-- Copy Script -->
    <script>
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied: ' + text);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }
    </script>


        <style>
        .nav-logo-animate:hover, .nav-logo-animate:focus {
            transform: scale(1.13) rotate(-2deg);
            z-index: 2;
        }
        .nav-logo-animate img, .nav-logo-animate .nav-logo-text {
            transition:transform 0.35s cubic-bezier(.4,1.6,.6,1);
        }

        /* Pop-up animation for navbar tabs */
        .navbar-nav .nav-link {
            transition: transform 0.25s cubic-bezier(.4,1.6,.6,1), background 0.2s, color 0.2s;
            border-radius: 8px;
            position: relative;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link:focus {
            transform: scale(1.11) translateY(-2px);
            background: #f8bbd0;
            color: #e83e8c !important;
            z-index: 1;
        }
        /* Active tab color */
        .navbar-nav .nav-link.active, .navbar-nav .nav-link[aria-current="page"] {
            background: #fff;
            color: #e83e8c !important;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(232,62,140,0.08);
        }
        /*drop down*/
        @media (min-width: 992px) {
            .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>