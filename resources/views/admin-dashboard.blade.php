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
        @include('navbar')
        <div class="container py-5">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold text-pink">Admin Dashboard</h2>
                    <p class="text-muted">Welcome to the Lourdes College Library Admin Panel. Use the sidebar to manage system resources.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">User Management</h5>
                            <p class="card-text">Manage users, roles, and permissions.</p>
                            <a href="{{ route('user.management') }}" class="btn btn-pink">Go to Users</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">Mides Management</h5>
                            <p class="card-text">Oversee thesis, dissertations, and research papers.</p>
                            <a href="{{ route('mides.management') }}" class="btn btn-pink">Go to Mides</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">Alert Services</h5>
                            <p class="card-text">Manage library alerts and notifications.</p>
                            <a href="{{ route('alert-services.manage') }}" class="btn btn-pink">Go to Alerts</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">ALINET Appointments</h5>
                            <p class="card-text">View and manage ALINET appointments.</p>
                            <a href="{{ route('alinet.manage') }}" class="btn btn-pink">Go to ALINET</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">Post Management</h5>
                            <p class="card-text">Manage posts, events, and updates.</p>
                            <a href="{{ route('post.management') }}" class="btn btn-pink">Go to Posts</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">Sidlak Journals</h5>
                            <p class="card-text">Manage Sidlak journal entries.</p>
                            <a href="{{ route('sidlak.manage') }}" class="btn btn-pink">Go to Sidlak</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title text-pink">Mides Categories</h5>
                            <p class="card-text">Manage Mides categories and tags.</p>
                            <a href="{{ route('mides.categories.panel') }}" class="btn btn-pink">Go to Categories</a>
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
