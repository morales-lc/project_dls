<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div id="dashboardWrapper" class="d-flex position-relative">
    @include('components.admin-sidebar')
    <div class="flex-grow-1">
       
        <div class="container py-5">
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
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        function toggleSidebar() {
            sidebar.classList.toggle('sidebar-collapsed');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                sidebar.style.transform = 'translateX(-100%)';
            } else {
                sidebar.style.transform = 'translateX(0)';
            }
        }
        sidebarToggle.addEventListener('click', function() {
            toggleSidebar();
        });
        // Responsive: hide sidebar by default on mobile
        function handleResize() {
            if (window.innerWidth < 992) {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.style.transform = 'translateX(-100%)';
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.style.transform = 'translateX(0)';
            }
        }
        window.addEventListener('resize', handleResize);
        document.addEventListener('DOMContentLoaded', handleResize);
    </script>
</body>
</html>
            

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
