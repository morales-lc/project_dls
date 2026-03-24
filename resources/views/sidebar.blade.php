<!-- Desktop sidebar (shown on md and up). On small screens we'll use an offcanvas. -->
<div class="d-none d-md-block">
    <div class="sidebar bg-white border-end vh-100 p-4 d-flex flex-column shadow-sm" style="width: 240px; border-radius: 18px 0 0 18px; min-height: 80vh;">
        @php
        $sf = auth()->user()->studentFaculty ?? null;
        $fullName = trim(($sf->first_name ?? '') . ' ' . ($sf->last_name ?? '')) ?: auth()->user()->name;
        $profilePic = $sf->profile_picture ?? null;
        $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
        $profileUrl = $profilePic ? ($isGooglePic ? $profilePic : asset('storage/profile_pictures/' . $profilePic)) : 'https://ui-avatars.com/api/?name=' . urlencode($fullName);
    // Bookmark count for the sidebar badge
    $bookmarkCount = 0;
    $historyCount = 0;
    $cartCount = 0;
    $canUseCart = in_array(auth()->user()->role ?? '', ['student', 'faculty'], true);
    if ($sf) {
    try {
    $bookmarkCount = \App\Models\Bookmark::where('student_faculty_id', $sf->id)->count();
    } catch (\Throwable $e) {
    // gracefully ignore DB errors in sidebar
    $bookmarkCount = 0;
    }
    try {
    $historyCount = \App\Models\SearchHistory::where('student_faculty_id', $sf->id)->count();
    } catch (\Throwable $e) {
    $historyCount = 0;
    }
    if ($canUseCart) {
    try {
    $cartCount = \App\Models\CartItem::where('student_faculty_id', $sf->id)->count();
    } catch (\Throwable $e) {
    $cartCount = 0;
    }
    }
    }
        @endphp

        <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: #f8bbd0;">
            <a href="{{ route('profile') }}" class="flex-shrink-0 me-3">
                <img src="{{ $profileUrl }}" alt="Profile" class="rounded-circle" width="56" height="56" style="object-fit:cover;">
            </a>
            <div class="flex-grow-1" style="min-width:0;">
                <a href="{{ route('profile') }}" class="text-dark fw-bold d-block text-truncate" style="text-decoration:none; max-width:160px;">{{ $fullName }}</a>
                <div class="small text-muted" style="max-width:180px; word-break:break-word; overflow-wrap:anywhere;">{{ auth()->user()->email }}</div>
            </div>
        </div>

        <ul class="nav flex-column mb-3 mt-3">
            <li class="nav-item mb-3">
                <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('profile') ? 'bg-light' : '' }}" href="{{ route('profile') }}">
                    <i class="bi bi-person-circle me-2 fs-5"></i>
                    <span>My Account</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('bookmarks.*') ? 'bg-light' : '' }}" href="{{ route('bookmarks.index') }}">
                    <i class="bi bi-bookmark-heart me-2 fs-5"></i>
                    <span>Bookmarked Items</span>
                    @if($bookmarkCount > 0)
                    <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $bookmarkCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('history') ? 'bg-light' : '' }}" href="{{ route('history') }}">
                    <i class="bi bi-clock-history me-2 fs-5"></i>
                    <span>Search History</span>
                    @if($historyCount > 0)
                    <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $historyCount }}</span>
                    @endif
                </a>
            </li>
            @if($canUseCart)
            <li class="nav-item mb-3">
                <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('cart.*') ? 'bg-light' : '' }}" href="{{ route('cart.index') }}">
                    <i class="bi bi-cart3 me-2 fs-5"></i>
                    <span>My Cart</span>
                    @if($cartCount > 0)
                    <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $cartCount }}</span>
                    @endif
                </a>
            </li>
            @endif

        </ul>


    </div>
</div>

<!-- Offcanvas mobile sidebar (shown on small screens) -->
<div class="d-md-none">
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="sidebar bg-white h-100 p-4 d-flex flex-column">
                <!-- ...existing sidebar content... -->
                @php
                $sf = auth()->user()->studentFaculty ?? null;
                $fullName = trim(($sf->first_name ?? '') . ' ' . ($sf->last_name ?? '')) ?: auth()->user()->name;
                $profilePic = $sf->profile_picture ?? null;
                $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                $profileUrl = $profilePic ? ($isGooglePic ? $profilePic : asset('storage/profile_pictures/' . $profilePic)) : 'https://ui-avatars.com/api/?name=' . urlencode($fullName);
                // Bookmark count for the sidebar badge
                $bookmarkCount = 0;
                $historyCount = 0;
                $cartCount = 0;
                $canUseCart = in_array(auth()->user()->role ?? '', ['student', 'faculty'], true);
                if ($sf) {
                try {
                $bookmarkCount = \App\Models\Bookmark::where('student_faculty_id', $sf->id)->count();
                } catch (\Throwable $e) {
                // gracefully ignore DB errors in sidebar
                $bookmarkCount = 0;
                }
                try {
                $historyCount = \App\Models\SearchHistory::where('student_faculty_id', $sf->id)->count();
                } catch (\Throwable $e) {
                $historyCount = 0;
                }
                if ($canUseCart) {
                try {
                $cartCount = \App\Models\CartItem::where('student_faculty_id', $sf->id)->count();
                } catch (\Throwable $e) {
                $cartCount = 0;
                }
                }
                }
                @endphp

                <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: #f8bbd0;">
                    <a href="{{ route('profile') }}" class="flex-shrink-0 me-3">
                        <img src="{{ $profileUrl }}" alt="Profile" class="rounded-circle" width="56" height="56" style="object-fit:cover;">
                    </a>
                    <div class="flex-grow-1" style="min-width:0;">
                        <a href="{{ route('profile') }}" class="text-dark fw-bold d-block text-truncate" style="text-decoration:none; max-width:160px;">{{ $fullName }}</a>
                        <div class="small text-muted" style="max-width:180px; word-break:break-word; overflow-wrap:anywhere;">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <ul class="nav flex-column mb-3 mt-3">
                    <li class="nav-item mb-3">
                        <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('profile') ? 'bg-light' : '' }}" href="{{ route('profile') }}">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <span>My Account</span>
                        </a>
                    </li>
                    <li class="nav-item mb-3">
                        <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('bookmarks.*') ? 'bg-light' : '' }}" href="{{ route('bookmarks.index') }}">
                            <i class="bi bi-bookmark-heart me-2 fs-5"></i>
                            <span>Bookmarked Items</span>
                            @if($bookmarkCount > 0)
                            <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $bookmarkCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item mb-3">
                        <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('history') ? 'bg-light' : '' }}" href="{{ route('history') }}">
                                <i class="bi bi-clock-history me-2 fs-5"></i>
                                <span>Search History</span>
                                @if($historyCount > 0)
                                <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $historyCount }}</span>
                                @endif
                            </a>
                    </li>
                    @if($canUseCart)
                    <li class="nav-item mb-3">
                        <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('cart.*') ? 'bg-light' : '' }}" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 me-2 fs-5"></i>
                            <span>My Cart</span>
                            @if($cartCount > 0)
                            <span class="badge rounded-pill bg-pink text-white ms-auto">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Floating toggle button for mobile to show/hide sidebar (moved to top-left) -->
<button id="mobileSidebarToggle" class="btn btn-pink d-md-none position-fixed" style="left:18px; top:60px; z-index:2050; border-radius:10px; width:46px; height:46px; display:flex; align-items:center; justify-content:center; box-shadow:0 6px 18px rgba(232,62,140,0.18);">
    <i class="bi bi-list fs-5" style="color:#fff;"></i>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('mobileSidebarToggle');
        var offcanvasEl = document.getElementById('mobileSidebar');
        if (!toggle || !offcanvasEl) return;
        // create one Offcanvas instance and reuse it
        var off = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        toggle.addEventListener('click', function() {
            off.toggle();
        });
    });
</script>