<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librarian Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('navbar')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Librarian Dashboard</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h4 class="mb-3">Mides Management</h4>
                    <a href="{{ route('mides.management') }}" class="btn btn-primary w-100">Go to Mides Management</a>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4 class="mb-3">Post Management</h4>
                    <a href="{{ route('post.management') }}" class="btn btn-success w-100">Go to Post Management</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
