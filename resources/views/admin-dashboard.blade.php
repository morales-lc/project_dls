@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-5">
    @php
    $studentsCount = \App\Models\StudentFaculty::where('role', 'student')->count();
    $facultiesCount = \App\Models\StudentFaculty::where('role', 'faculty')->count();
    $alinetPendingCount = \App\Models\AlinetAppointment::where('status', 'pending')->count();
    $feedbackCount = \App\Models\Feedback::count();
    $libraryStaffCount = \App\Models\LibraryStaff::count();
    $librarianCount = \App\Models\User::where('role','librarian')->count();
    // LiRA status counters
    $liraPendingCount = \App\Models\LiraRequest::where('status', 'pending')->count();
    $liraAcceptedCount = \App\Models\LiraRequest::where('status', 'accepted')->count();
    $liraRejectedCount = \App\Models\LiraRequest::where('status', 'rejected')->count();
    // SIDLak and MIDES counters
    $sidlakJournalCount = \App\Models\SidlakJournal::count();
    $sidlakArticleCount = \App\Models\SidlakArticle::count();
    $midesDocumentCount = \App\Models\MidesDocument::count();
    // Catalog counters (guard for missing timestamp columns)
    $catalogCount = \App\Models\Catalog::count();
    $catalogLastUpdated = null;
    try {
    if (\Illuminate\Support\Facades\Schema::hasTable('catalogs')) {
    if (\Illuminate\Support\Facades\Schema::hasColumn('catalogs', 'updated_at')) {
    $catalogLastUpdated = \App\Models\Catalog::max('updated_at');
    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('catalogs', 'created_at')) {
    $catalogLastUpdated = \App\Models\Catalog::max('created_at');
    }
    }
    } catch (\Throwable $e) {
    $catalogLastUpdated = null;
    }

    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold text-pink mb-0">Admin Dashboard</h2>
            <small class="text-muted">Updated {{ now()->format('M d, Y h:i A') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.management') }}" class="btn btn-outline-pink"><i class="bi bi-people me-2"></i>Users</a>
            <a href="{{ route('mides.management') }}" class="btn btn-outline-pink"><i class="bi bi-journal-text me-2"></i>MIDES</a>
            <a href="{{ route('alinet.manage') }}" class="btn btn-outline-pink"><i class="bi bi-calendar-check me-2"></i>ALINET</a>
        </div>
    </div>
    {{-- Combined solid cards --}}
    <div class="row g-4 mt-1">
        {{-- Users (Students + Faculty + Librarians) --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-blue solid">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">User Accounts</div>
                        <div class="stat-metrics mt-2">
                            <div class="stat-metric">
                                <div class="label">Students</div>
                                <div class="value">{{ $studentsCount }}</div>
                            </div>
                            <div class="stat-metric">
                                <div class="label">Faculty</div>
                                <div class="value">{{ $facultiesCount }}</div>
                            </div>
                            <div class="stat-metric">
                                <div class="label">Librarians</div>
                                <div class="value">{{ $librarianCount }}</div>
                            </div>
                        </div>
                        <a href="{{ route('user.management') }}" class="small d-inline-block mt-2">Manage users →</a>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- LiRA Statuses (Pending/Accepted/Rejected) --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-amber solid">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">LiRA Requests (by status)</div>
                        <div class="stat-metrics mt-2">
                            <div class="stat-metric">
                                <div class="label">Pending</div>
                                <div class="value">{{ $liraPendingCount }}</div>
                            </div>
                            <div class="stat-metric">
                                <div class="label">Accepted</div>
                                <div class="value">{{ $liraAcceptedCount }}</div>
                            </div>
                            <div class="stat-metric">
                                <div class="label">Rejected</div>
                                <div class="value">{{ $liraRejectedCount }}</div>
                            </div>
                        </div>
                        <a href="{{ route('lira.manage') }}" class="small d-inline-block mt-2">Open LiRA manager →</a>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-inboxes"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- SIDLAK (Journals + Articles) --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-indigo solid">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">SIDLAK Content</div>
                        <div class="stat-metrics mt-2">
                            <div class="stat-metric">
                                <div class="label">Journals</div>
                                <div class="value">{{ $sidlakJournalCount }}</div>
                            </div>
                            <div class="stat-metric">
                                <div class="label">Articles</div>
                                <div class="value">{{ $sidlakArticleCount }}</div>
                            </div>
                        </div>
                        <a href="{{ route('sidlak.manage') }}" class="small d-inline-block mt-2">Manage SIDLAK →</a>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        {{-- MIDES --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-teal solid">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">MIDES Documents</div>
                        <div class="h2 fw-bold mb-0 mt-2">{{ $midesDocumentCount }}</div>
                        <a href="{{ route('mides.management') }}" class="small d-inline-block mt-2">Manage MIDES →</a>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALINET Pending --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-red solid position-relative">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">ALINET Appointments</div>
                        <div class="h2 fw-bold mb-0 mt-2">{{ $alinetPendingCount }}</div>
                        <div class="small">Pending</div>
                        <a href="{{ route('alinet.manage', ['status' => 'pending']) }}" class="small d-inline-block mt-2">Manage appointments →</a>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <a href="{{ route('alinet.manage', ['status' => 'pending']) }}" class="stretched-link" aria-label="Open ALINET (Pending)"></a>
                </div>
            </div>
        </div>

        {{-- Feedback --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-purple solid">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">User Feedback</div>
                        <div class="h2 fw-bold mb-0 mt-2">{{ $feedbackCount }}</div>
                        <a href="{{ route('feedback.admin') }}" class="small d-inline-block mt-2">View feedback →</a>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catalogs --}}
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-green solid">
                <div class="card-body d-flex align-items-start justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small">Library Catalogs</div>
                        <div class="h2 fw-bold mb-0 mt-2">{{ $catalogCount }}</div>
                        @if($catalogLastUpdated)
                        <div class="small">Last updated {{ \Carbon\Carbon::parse($catalogLastUpdated)->format('M d, Y h:i A') }}</div>
                        @else
                        <div class="small">No updates yet</div>
                        @endif
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 text-pink">Quick Actions</h5>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('alert-services.manage') }}" class="btn btn-outline-pink"><i class="bi bi-bell me-2"></i>Alert Services</a>
                        <a href="{{ route('post.management') }}" class="btn btn-outline-pink"><i class="bi bi-file-earmark-post me-2"></i>Posts</a>
                        <a href="{{ route('library.content.manage') }}" class="btn btn-outline-pink"><i class="bi bi-collection-play me-2"></i>Library Content</a>
                        <a href="{{ route('sidlak.manage') }}" class="btn btn-outline-pink"><i class="bi bi-journal-richtext me-2"></i>Sidlak</a>
                        <a href="{{ route('mides.categories.panel') }}" class="btn btn-outline-pink"><i class="bi bi-tags me-2"></i>MIDES Categories</a>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 text-pink">System Notes</h5>
                    </div>
                    <p class="text-muted mb-0">Welcome back. Use the quick actions to jump into common tasks or explore the sidebar for full controls.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('management-scripts')
@endpush