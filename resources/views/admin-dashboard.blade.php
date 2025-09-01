@include('navbar')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Admin Dashboard</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('mides.categories.panel') }}" class="btn btn-success w-100 py-4">
                <i class="bi bi-list-ul me-2"></i>Manage MIDES Categories
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('post.management') }}" class="btn btn-primary w-100 py-4">
                <i class="bi bi-file-earmark-post me-2"></i>Manage Posts
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('user.management') }}" class="btn btn-info w-100 py-4">
                <i class="bi bi-people me-2"></i>User Management
            </a>
        </div>
    </div>
    <div class="mt-5 text-center">
        <a href="{{ route('mides.management') }}" class="btn btn-secondary">Back to Repository Management</a>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
