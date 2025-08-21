<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Graduate Theses Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .category-card {
            background: #f3f4f6;
            border-radius: 10px;
            border: 2px solid transparent;
            transition: border-color 0.2s;
            cursor: pointer;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .category-card:hover, .category-card.active {
            border-color: #d32f2f;
            background: #fff;
        }
        .category-icon {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
@include('navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Graduate Theses</h2>
        <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-primary">MIDES Dashboard</a>
    </div>
    <div class="row g-4">
        @foreach($categories as $cat)
        <div class="col-md-4 col-12">
            <a href="{{ route('mides.graduate.category', ['category' => $cat]) }}" class="text-decoration-none">
                <div class="category-card">
                    <span class="category-icon">
                        @if(str_contains(strtolower($cat), 'library'))
                            <i class="bi bi-journal-bookmark"></i>
                        @elseif(str_contains(strtolower($cat), 'business'))
                            <i class="bi bi-building"></i>
                        @elseif(str_contains(strtolower($cat), 'hospitality'))
                            <i class="bi bi-person-workspace"></i>
                        @else
                            <i class="bi bi-book"></i>
                        @endif
                    </span>
                    <div class="fw-bold text-center">{{ $cat }}</div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
