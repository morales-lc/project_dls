<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Navbar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <style>
      .nav-logo-animate:hover { transform: scale(1.13) rotate(-2deg); }
      .navbar-nav .nav-link {
          transition: transform 0.25s cubic-bezier(.4,1.6,.6,1), background 0.2s, color 0.2s;
          border-radius: 8px;
          position: relative;
      }
      .navbar-nav .nav-link:hover {
          transform: scale(1.1);
          background: #f8bbd0;
          color: #e83e8c !important;
      }
      .navbar-nav .nav-link.active {
          background: #fff;
          color: #e83e8c !important;
          font-weight: bold;
          box-shadow: 0 2px 8px rgba(232,62,140,0.08);
      }

      /* NOTE: We no longer use CSS-only hover to show dropdowns (it conflicts with Bootstrap's JS).
         Instead the JS below will open dropdowns on hover on desktop (non-touch) and keep default click behavior on mobile. */

      /* Ensure dropdown menus render above other content */
      .dropdown-menu {
          z-index: 2050;
      }

      /* Profile image style */
      .profile-pic {
          border: 2px solid #fff;
          box-shadow: 0 2px 8px rgba(0,0,0,0.08);
          transition: box-shadow 0.2s;
      }
      .profile-pic:hover {
          box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark px-3" style="background-color: #e83e8c !important;">
  <div class="container-fluid">
      <!-- Logo and Brand -->
      <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
          <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38" class="rounded" style="background:#fff; padding:2px;">
          <span class="d-none d-md-inline">LC MIDES Digital Library</span>
      </a>

      <!-- Toggler -->
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Home</a>
              </li>

              <!-- About Dropdown -->
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

              <!-- Libraries Dropdown -->
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle {{ request()->routeIs('wiley.*','gale.*','proquest.*') ? 'active' : '' }}" href="#" id="librariesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Libraries
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="librariesDropdown">
                      <li><a class="dropdown-item" href="{{ route('libraries.college') }}">College Library</a></li>
                      <li><a class="dropdown-item" href="{{ route('libraries.graduate') }}">Graduate Library</a></li>
                      <li><a class="dropdown-item" href="{{ route('libraries.senior_high') }}">Senior High School Library</a></li>
                      <li><a class="dropdown-item" href="{{ route('libraries.ibed') }}">IBED Library</a></li>
                      <li><a class="dropdown-item" href="{{ route('elibraries') }}">Online E-Libraries</a></li>
                  </ul>
              </li>

              <!-- Services Dropdown -->
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle {{ request()->routeIs('alert-services.*','alinet.form','learning-spaces') ? 'active' : '' }}" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Services
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                      <li><a class="dropdown-item" href="{{ route('lira.form') }}">LiRA</a></li>
                      <li><a class="dropdown-item" href="{{ route('alert-services.index') }}">Alert Services</a></li>
                      <li><a class="dropdown-item" href="{{ route('alinet.form') }}">ALINET</a></li>
                      <li><a class="dropdown-item" href="#">Book borrowing</a></li>
                      <li><a class="dropdown-item" href="{{ route('information_literacy.index') }}">Information Literacy Alert Schedule</a></li>
                      <li><a class="dropdown-item" href="#">Scanning Services</a></li>
                      <li><a class="dropdown-item {{ request()->routeIs('learning-spaces') ? 'active' : '' }}" href="{{ route('learning-spaces') }}">Learning Spaces</a></li>
                  </ul>
              </li>

              <!-- E-Resources Dropdown -->
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle {{ request()->routeIs('mides.dashboard','sidlak.index') ? 'active' : '' }}" href="#" id="eresourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Electronic Resources
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="eresourcesDropdown">
                      <li><a class="dropdown-item" href="{{ route('mides.dashboard') }}">MIDES repository</a></li>
                      <li><a class="dropdown-item" href="{{ route('sidlak.index') }}">SIDLAk</a></li>
                  </ul>
              </li>
          </ul>

          <!-- Profile / Login -->
          <div class="d-flex align-items-center justify-content-center justify-content-lg-end mt-3 mt-lg-0">
              @if(session()->has('login') || Auth::check())
                  @php
                      $profilePic = Auth::user()->studentFaculty->profile_picture ?? null;
                      $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                      $fullName = trim((Auth::user()->studentFaculty->first_name ?? '') . ' ' . (Auth::user()->studentFaculty->last_name ?? ''));
                  @endphp
                  <div class="dropdown text-center text-lg-start">
                      <a class="d-flex align-items-center text-white dropdown-toggle gap-2" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                          <li><a class="dropdown-item" href="{{ route('settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li>
                              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                                  @csrf
                                  <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                              </form>
                          </li>
                      </ul>
                  </div>
              @else
                  <a href="{{ route('login') }}" class="btn btn-outline-light ms-2">Login</a>
              @endif
          </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Small script to stabilize dropdown behavior (prevent href="#" jump, and enable safe hover on desktop) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Prevent href="#" anchors from jumping the page for dropdown toggles
    document.querySelectorAll('.dropdown-toggle[href="#"]').forEach(function (el) {
        el.addEventListener('click', function (ev) {
            // let bootstrap handle the toggle; prevent the '#' default jump
            if (this.getAttribute('href') === '#') {
                ev.preventDefault();
            }
        });
    });

    // Helper to detect desktop (non-touch) large screens
    function isDesktopForHover() {
        return window.matchMedia('(min-width: 992px)').matches && !('ontouchstart' in window);
    }

    // Attach mouseenter / mouseleave to show/hide dropdowns using Bootstrap API (no CSS-only hack)
    document.querySelectorAll('.navbar .dropdown').forEach(function (dropdown) {
        dropdown.addEventListener('mouseenter', function () {
            if (!isDesktopForHover()) return;
            var toggle = dropdown.querySelector('.dropdown-toggle');
            // use Bootstrap API to show
            bootstrap.Dropdown.getOrCreateInstance(toggle).show();
        });
        dropdown.addEventListener('mouseleave', function () {
            if (!isDesktopForHover()) return;
            var toggle = dropdown.querySelector('.dropdown-toggle');
            bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
        });
    });

    // Close any open dropdown when clicking outside (extra safe)
    document.addEventListener('click', function (e) {
        var open = document.querySelectorAll('.navbar .dropdown.show');
        open.forEach(function (d) {
            if (!d.contains(e.target)) {
                var toggle = d.querySelector('.dropdown-toggle');
                bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
            }
        });
    });
});
</script>

</body>
</html>
