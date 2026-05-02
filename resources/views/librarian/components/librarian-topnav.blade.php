<nav id="librarianTopnav" class="navbar navbar-expand-lg navbar-light" style="position:fixed; top:0; left:0; right:0; z-index:1050; background: linear-gradient(90deg, #f8bbd0 0%, #e83e8c 100%); border-bottom: 2px solid #d81b60; box-shadow: 0 4px 16px rgba(232,62,140,0.10); height:72px;">
    @php
        $newLiraCount = \App\Models\LiraRequest::where(function ($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })->count();
        $newAlinetCount = \App\Models\AlinetAppointment::where(function ($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })->count();
        $newFeedbackCount = \App\Models\Feedback::query()
            ->threads()
            ->where('status', 'open')
            ->count();
        $newFeedbackCommentCount = \App\Models\Feedback::query()
            ->replies()
            ->whereHas('parent', function ($q) {
                $q->threads()->where('status', 'open');
            })
            ->where(function ($q) {
                $q->whereNull('role')->orWhereNotIn('role', ['admin', 'librarian']);
            })
            ->count();
        $totalNewRequests = $newLiraCount + $newAlinetCount + $newFeedbackCount + $newFeedbackCommentCount;
    @endphp
    <div class="container-fluid px-4">
        <button id="sidebarToggleBtnTop" class="btn btn-outline-pink d-inline-flex me-3" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-2"></i>
        </button>
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('librarian.dashboard') }}" style="color:#d81b60;">
            <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38" style="object-fit:contain; border-radius:8px; background:#fff; padding:2px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <span class="d-none d-md-inline" style="font-weight:600; color:#d81b60;">Librarian Panel</span>
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light position-relative d-flex align-items-center justify-content-center rounded-3" type="button" id="librarianNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="New requests" title="New requests" style="width:44px; height:44px; border:1.5px solid #ffd1e3; box-shadow:0 2px 8px rgba(232,62,140,0.06);">
                    <i class="bi bi-bell" style="font-size:1.25rem; color:#d81b60;"></i>
                    <span id="librarianNotificationTotalBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $totalNewRequests > 0 ? '' : 'd-none' }}" style="font-size:0.7rem; min-width:1.2rem;">
                        {{ $totalNewRequests > 99 ? '99+' : $totalNewRequests }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="librarianNotificationDropdown" style="min-width: 250px;">
                    <li class="px-3 py-2 small text-muted d-flex align-items-center justify-content-between">
                        <span>New Requests</span>
                        <span id="librarianNotificationLoadingSpinner" class="d-none" aria-hidden="true">
                            <span class="spinner-border spinner-border-sm text-danger" style="width:0.8rem; height:0.8rem;"></span>
                        </span>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('alinet.manage', ['status' => 'pending']) }}">
                            <span><i class="bi bi-calendar-check me-2"></i>ALINET</span>
                            <span id="librarianNotificationAlinetBadge" class="badge bg-danger rounded-pill">{{ $newAlinetCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('lira.manage') }}">
                            <span><i class="bi bi-journal me-2"></i>LiRA</span>
                            <span id="librarianNotificationLiraBadge" class="badge bg-danger rounded-pill">{{ $newLiraCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('feedback.admin') }}">
                            <span><i class="bi bi-chat-dots me-2"></i>Feedback</span>
                            <span id="librarianNotificationFeedbackBadge" class="badge bg-danger rounded-pill">{{ $newFeedbackCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('feedback.admin') }}">
                            <span><i class="bi bi-chat-left-text me-2"></i>Comments</span>
                            <span id="librarianNotificationFeedbackCommentBadge" class="badge bg-danger rounded-pill">{{ $newFeedbackCommentCount }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2 px-3 py-2 rounded-3" type="button" id="librarianProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border:1.5px solid #ffd1e3; box-shadow:0 2px 8px rgba(232,62,140,0.06);">
                    <i class="bi bi-person-circle" style="font-size:1.5rem; color:#d81b60;"></i>
                    <span class="d-none d-md-inline" style="color:#d81b60; font-weight:500;">{{ Auth::user()->name ?? 'Librarian' }}</span>
                    <i class="bi bi-caret-down-fill ms-1" style="font-size:0.9rem; color:#d81b60;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="librarianProfileDropdown">
                    <li><a class="dropdown-item" href="{{ route('librarian.profile') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.analytics') }}"><i class="bi bi-bar-chart me-2"></i>Analytics</a></li>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownButton = document.getElementById('librarianNotificationDropdown');
    const loadingSpinner = document.getElementById('librarianNotificationLoadingSpinner');
    const totalBadge = document.getElementById('librarianNotificationTotalBadge');
    const alinetBadge = document.getElementById('librarianNotificationAlinetBadge');
    const liraBadge = document.getElementById('librarianNotificationLiraBadge');
    const feedbackBadge = document.getElementById('librarianNotificationFeedbackBadge');
    const feedbackCommentBadge = document.getElementById('librarianNotificationFeedbackCommentBadge');
    const pendingCountsUrl = @json(route('staff.notifications.pending-counts'));

    if (!dropdownButton || !loadingSpinner || !totalBadge || !alinetBadge || !liraBadge || !feedbackBadge || !feedbackCommentBadge) {
        return;
    }

    let isLoading = false;

    const formatCount = (count) => {
        return count > 99 ? '99+' : String(count);
    };

    const toNumber = (value) => {
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : 0;
    };

    const refreshNotificationBadges = async () => {
        if (isLoading) {
            return;
        }

        isLoading = true;
        loadingSpinner.classList.remove('d-none');

        try {
            const response = await fetch(pendingCountsUrl, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to fetch pending notification counts.');
            }

            const data = await response.json();
            const newLiraCount = toNumber(data.newLiraCount);
            const newAlinetCount = toNumber(data.newAlinetCount);
            const newFeedbackCount = toNumber(data.newFeedbackCount);
            const newFeedbackCommentCount = toNumber(data.newFeedbackCommentCount);
            const totalNewRequests = toNumber(data.totalNewRequests);

            liraBadge.textContent = String(newLiraCount);
            alinetBadge.textContent = String(newAlinetCount);
            feedbackBadge.textContent = String(newFeedbackCount);
            feedbackCommentBadge.textContent = String(newFeedbackCommentCount);
            totalBadge.textContent = formatCount(totalNewRequests);
            totalBadge.classList.toggle('d-none', totalNewRequests <= 0);
        } catch (error) {
            console.error(error);
        } finally {
            loadingSpinner.classList.add('d-none');
            isLoading = false;
        }
    };

    dropdownButton.addEventListener('show.bs.dropdown', refreshNotificationBadges);
});
</script>
