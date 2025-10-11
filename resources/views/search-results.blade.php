<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

@include('navbar') 

<div class="container mt-4">

    {{-- Search Bar --}}
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" value="Java Programming" placeholder="Search...">
        <select class="form-select">
            <option selected>SELECT TYPE</option>
            <option>Anytype</option>
            <option>Book</option>
        </select>
        <button class="btn btn-secondary">Search</button>
    </div>

    {{-- Result Summary --}}
    <div class="d-flex justify-content-between align-items-center bg-light px-3 py-2 mb-3">
        <div>Results: <strong>1-20 of 999</strong></div>
        <div>
            <i class="bi bi-list-ul me-2"></i>
            <i class="bi bi-grid-3x3-gap"></i>
        </div>
    </div>

    {{-- Result Item (Placeholder) --}}
@for($i = 0; $i < 4; $i++)
    <a href="{{ route('resource.view') }}" class="text-decoration-none text-dark">
        <div class="d-flex mb-3 border-bottom pb-3">
            <div class="me-3">
                <img src="{{ asset('images/placeholder.jpg') }}" alt="cover" class="img-fluid">
            </div>
            <div class="flex-grow-1">
                <h5><strong>Java Programming I - Intro to Programming</strong></h5>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse mauris nibh, convallis eget magna in...
                </p>
                <p class="mb-0">
                    <strong>Author:</strong> John Doe<br>
                    <strong>Published:</strong> 1999<br>
                    <strong>Sub location:</strong> 2F Learning Commons
                </p>
            </div>
            <div class="text-end">
                <p class="text-muted mb-1">Add to list</p>
                <i class="bi bi-plus-circle fs-4 text-secondary"></i>
            </div>
        </div>
    </a>
@endfor


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
