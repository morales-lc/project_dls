@include('navbar')
<link href="{{ asset('css/mides.css') }}" rel="stylesheet">
<div class="container py-4">
    <h2 class="mb-4 fw-bold">Senior High School Research Paper Programs</h2>

        <div class="col-md-12 text-center">
            <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-dark">Back to Dashboard</a>
        </div>

    <div style="height: 30px;"></div>
    <div class="row g-4 justify-content-center">
        @foreach($programs as $program)
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('mides.seniorhigh.program', ['program' => $program]) }}" class="text-decoration-none mides-card-link">
                <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                    <div class="card-body">
                        <span class="display-4 text-success">
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
                            <i class="bi {{ $icon }}"></i>
                        </span>
                        <h5 class="mt-3 mb-1 fw-bold">{{ $program }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

</div>