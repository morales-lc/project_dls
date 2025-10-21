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
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg, #fff 80%, #ffe3f1 100%);">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase small text-muted">Students</div>
                                <div class="h2 fw-bold mb-0">{{ $studentsCount }}</div>
                                <div class="text-muted small">Registered</div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:#ffd1e3;color:#d81b60;">
                                <i class="bi bi-mortarboard" style="font-size:1.4rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg, #fff 80%, #ffe3f1 100%);">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase small text-muted">Faculties</div>
                                <div class="h2 fw-bold mb-0">{{ $facultiesCount }}</div>
                                <div class="text-muted small">Registered</div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:#ffd1e3;color:#d81b60;">
                                <i class="bi bi-person-badge" style="font-size:1.4rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg, #fff 80%, #ffe3f1 100%);">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase small text-muted">ALINET Pending</div>
                                <div class="h2 fw-bold mb-0">{{ $alinetPendingCount }}</div>
                                <a href="{{ route('alinet.manage') }}" class="small">Manage appointments →</a>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:#ffd1e3;color:#d81b60;">
                                <i class="bi bi-calendar3" style="font-size:1.4rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg, #fff 80%, #ffe3f1 100%);">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase small text-muted">Feedback</div>
                                <div class="h2 fw-bold mb-0">{{ $feedbackCount }}</div>
                                <a href="{{ route('feedback.admin') }}" class="small">View feedback →</a>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:#ffd1e3;color:#d81b60;">
                                <i class="bi bi-chat-dots" style="font-size:1.4rem;"></i>
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
                                    <a href="{{ route('admin.contact-info') }}" class="btn btn-outline-pink"><i class="bi bi-telephone me-2"></i>Contact Info</a>
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

