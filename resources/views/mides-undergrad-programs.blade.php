<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Undergraduate Baby Theses Programs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
    <style>
        .program-card {
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
        .program-card:hover, .program-card.active {
            border-color: #1976d2;
            background: #fff;
        }
        .program-icon {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
@include('navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Undergraduate Baby Theses Programs</h2>
        <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-primary">MIDES Dashboard</a>
    </div>
    <div class="row g-4">
        @foreach($programs as $prog)
        <div class="col-md-4 col-12">
            <a href="{{ route('mides.undergrad.program', ['program' => $prog]) }}" class="text-decoration-none mides-card-link">
                <div class="program-card mides-card-hover">
                    <span class="program-icon">
                        @php
                            $icon = 'bi-book';
                            $p = strtolower($prog);
                            if(str_contains($p, 'nursing')) {
                                $icon = 'bi-heart-pulse';
                            } elseif(str_contains($p, 'nutrition')) {
                                $icon = 'bi-apple';
                            } elseif(str_contains($p, 'pharmacy')) {
                                $icon = 'bi-capsule';
                            } elseif(str_contains($p, 'mass communication')) {
                                $icon = 'bi-mic';
                            } elseif(str_contains($p, 'library')) {
                                $icon = 'bi-journal-bookmark';
                            } elseif(str_contains($p, 'psychology')) {
                                $icon = 'bi-person';
                            } elseif(str_contains($p, 'accountancy')) {
                                $icon = 'bi-calculator';
                            } elseif(str_contains($p, 'business')) {
                                $icon = 'bi-building';
                            } elseif(str_contains($p, 'feasibility')) {
                                $icon = 'bi-bar-chart';
                            } elseif(str_contains($p, 'hotel')) {
                                $icon = 'bi-cup-hot';
                            } elseif(str_contains($p, 'tourism')) {
                                $icon = 'bi-geo-alt';
                            } elseif(str_contains($p, 'information technology') || str_contains($p, 'it')) {
                                $icon = 'bi-laptop';
                            } elseif(str_contains($p, 'social work')) {
                                $icon = 'bi-people';
                            } elseif(str_contains($p, 'elementary education')) {
                                $icon = 'bi-pencil';
                            } elseif(str_contains($p, 'secondary education')) {
                                $icon = 'bi-book-half';
                            } elseif(str_contains($p, 'english')) {
                                $icon = 'bi-book';
                            } elseif(str_contains($p, 'filipino')) {
                                $icon = 'bi-book';
                            }
                        @endphp
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <div class="fw-bold text-center">{{ $prog }}</div>
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
