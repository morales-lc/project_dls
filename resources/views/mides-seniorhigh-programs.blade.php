<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Senior High School Research Paper Programs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mides-scholar.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="mides-scholar">
@include('navbar')
<div class="container py-5 mides-wrap">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Senior High School Research Paper Programs</h2>
        <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-primary">MIDES Dashboard</a>
    </div>
    <div class="collection-list">
        @foreach($programs as $program)
            <a href="{{ route('mides.seniorhigh.program', ['program' => $program]) }}" class="collection-link">
                <div class="collection-item">
                    @php
                    $icon = 'bi-mortarboard';
                    $prog = strtolower($program);
                    if(str_contains($prog, 'abm')) {
                    $icon = 'bi-graph-up';
                    } elseif(str_contains($prog, 'humss')) {
                    $icon = 'bi-people';
                    } elseif(str_contains($prog, 'stem')) {
                    $icon = 'bi-cpu';
                    } elseif(str_contains($prog, 'tvl')) {
                    $icon = 'bi-tools';
                    } elseif(str_contains($prog, 'information computer technology')) {
                    $icon = 'bi-laptop';
                    } elseif(str_contains($prog, 'culinary')) {
                    $icon = 'bi-egg-fried';
                    }
                    @endphp
                    <h5 class="mb-1"><i class="bi {{ $icon }} me-2"></i>{{ $program }}</h5>
                    <div class="small text-muted">Browse research papers in this senior high program.</div>
                </div>
            </a>
        @endforeach
    </div>
</div>
<div style="height:100px;"></div>
@include('footer')
</body>
</html>