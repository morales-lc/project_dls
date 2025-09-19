@include('navbar')
<link href="{{ asset('css/mides.css') }}" rel="stylesheet">
<div class="container py-4">
    <h2 class="mb-4 fw-bold">Senior High School Research Paper Programs</h2>
    <div class="row g-4 justify-content-center">
        @foreach($programs as $program)
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('mides.seniorhigh.program', ['program' => $program]) }}" class="text-decoration-none mides-card-link">
                <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                    <div class="card-body">
                        <span class="display-4 text-success"><i class="bi bi-mortarboard"></i></span>
                        <h5 class="mt-3 mb-1 fw-bold">{{ $program }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="row mt-5">
        <div class="col-md-12 text-center">
            <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-dark">Back to Dashboard</a>
        </div>
    </div>
</div>

