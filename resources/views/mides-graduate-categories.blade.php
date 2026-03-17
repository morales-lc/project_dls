<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Graduate Theses Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
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
            <a href="{{ route('mides.graduate.category', ['category' => $cat]) }}" class="text-decoration-none mides-card-link">
                <div class="category-card mides-card-hover">
                    <span class="category-icon">
                        @php
                            $icon = 'bi-book';
                            $c = strtolower($cat);
                            if(str_contains($c, 'library')) {
                                $icon = 'bi-journal-bookmark';
                            } elseif(str_contains($c, 'business')) {
                                $icon = 'bi-building';
                            } elseif(str_contains($c, 'hospitality')) {
                                $icon = 'bi-person-workspace';
                            } elseif(str_contains($c, 'education')) {
                                $icon = 'bi-pencil';
                            } elseif(str_contains($c, 'english')) {
                                $icon = 'bi-book';
                            } elseif(str_contains($c, 'physical')) {
                                $icon = 'bi-bicycle';
                            } elseif(str_contains($c, 'human resource')) {
                                $icon = 'bi-people';
                            } elseif(str_contains($c, 'home economics')) {
                                $icon = 'bi-house-heart';
                            } elseif(str_contains($c, 'social work')) {
                                $icon = 'bi-people-fill';
                            }
                        @endphp
                        <i class="bi {{ $icon }}"></i>
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
