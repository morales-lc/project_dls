@include('navbar')
<style>
    .card.h-100 {
        transition: box-shadow .2s, transform .2s, border-color .2s;
        border: 2px solid transparent;
    }
    .card.h-100:hover {
        border-color: #388e3c;
        box-shadow: 0 0.5rem 1.5rem rgba(56, 142, 60, 0.15), 0 0.125rem 0.5rem rgba(0,0,0,0.08);
        transform: scale(1.04);
        z-index: 2;
    }
</style>
<div class="container py-4">
    <h2 class="mb-4 fw-bold">Senior High School Research Paper Programs</h2>
    <div class="row g-4 justify-content-center">
        @foreach($programs as $program)
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('mides.seniorhigh.program', ['program' => $program]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm text-center border-0" style="transition: box-shadow .2s;">
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

