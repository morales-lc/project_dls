<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mides</title>
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('navbar')
<div class="container py-5">
    <h2 class="fw-bold mb-2">Welcome to MIDES repository!</h2>
    <div class="mb-3 fs-5">
        Graduate Theses (contains abstracts, introduction and related literature of the theses completed for the M.A. programs in Lourdes College)
    </div>
    <div class="row mb-4 align-items-center">
        <div class="col-md-7 col-12 mb-2 mb-md-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="digital library system">
            </div>
        </div>
        <div class="col-md-3 col-8 mb-2 mb-md-0">
            <select class="form-select">
                <option selected>SELECT TYPE</option>
                <option>Thesis</option>
                <option>Dissertation</option>
            </select>
        </div>
        <div class="col-md-2 col-4">
            <button class="btn btn-secondary w-100">Search</button>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4 col-12">
            <div class="bg-light rounded-2 p-4 text-center h-100">
                <i class="bi bi-journal-bookmark fs-1 mb-2"></i>
                <div class="fw-bold">Masters in Library and Information Science</div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="bg-light rounded-2 p-4 text-center h-100">
                <i class="bi bi-building fs-1 mb-2"></i>
                <div class="fw-bold">Masters in Business Administration</div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="bg-light rounded-2 p-4 text-center h-100">
                <i class="bi bi-person-workspace fs-1 mb-2"></i>
                <div class="fw-bold">Masters of Science in Hospitality Management</div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="bg-light rounded-2 p-4 text-center h-100">
                <i class="bi bi-journal-bookmark fs-1 mb-2"></i>
                <div class="fw-bold">Masters in Library and Information Science</div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="bg-light rounded-2 p-4 text-center h-100">
                <i class="bi bi-building fs-1 mb-2"></i>
                <div class="fw-bold">Masters in Business Administration</div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="bg-light rounded-2 p-4 text-center h-100">
                <i class="bi bi-person-workspace fs-1 mb-2"></i>
                <div class="fw-bold">Masters of Science in Hospitality Management</div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


</body>
</html>

