<div class="sidebar bg-white border-end vh-100 p-4 d-flex flex-column shadow-sm" style="width: 240px; border-radius: 18px 0 0 18px; min-height: 80vh;">
    @php
        $sf = auth()->user()->studentFaculty ?? null;
        $fullName = trim(($sf->first_name ?? '') . ' ' . ($sf->last_name ?? '')) ?: auth()->user()->name;
        $profilePic = $sf->profile_picture ?? null;
        $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
        $profileUrl = $profilePic ? ($isGooglePic ? $profilePic : asset('storage/profile_pictures/' . $profilePic)) : 'https://ui-avatars.com/api/?name=' . urlencode($fullName);
        // Bookmark count for the sidebar badge
        $bookmarkCount = 0;
        if ($sf) {
            try {
                $bookmarkCount = \App\Models\Bookmark::where('student_faculty_id', $sf->id)->count();
            } catch (\Throwable $e) {
                // gracefully ignore DB errors in sidebar
                $bookmarkCount = 0;
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
            </a>
        </li>
    <li class="nav-item mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded {{ request()->routeIs('settings') ? 'bg-light' : '' }}" href="{{ route('settings') }}">
                <i class="bi bi-gear me-2 fs-5"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>


</div>
