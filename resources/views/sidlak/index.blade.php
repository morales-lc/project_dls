<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SIDLAK Journals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidlak.css') }}" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('navbar')
<div class="container py-5 mb-4" >
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1" style="color:#e83e8c;">SIDLAK</h2>
        <div class="fs-5 fw-semibold mb-1" style="color:#e83e8c;">The Lourdes College Multidisciplinary Research Journal</div>
        <div class="text-muted mb-2 mx-auto" style="max-width:700px;">
            SIDLAK (effulgence or radiance) refers to the light that radiates from the sun. It also refers to the quality of being bright and sending out rays of light. SIDLAK publishes relevant studies in various disciplines to advance the development of knowledge and inform diverse professional practice. This volume presents studies in social work, library and information science, hospitality management, home economics, and language teaching.
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
        <div class="card-body">
            <form method="GET" action="{{ route('sidlak.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold mb-1">Search</label>
                    <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Search by title or ISSN">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold mb-1">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        @foreach(($years ?? []) as $year)
                            <option value="{{ $year }}" {{ (string)($selectedYear ?? '') === (string)$year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold mb-1">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        @foreach(($months ?? []) as $month)
                            <option value="{{ $month }}" {{ ($selectedMonth ?? '') === $month ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-dark" type="submit">Apply</button>
                </div>
                <div class="col-12">
                    <a href="{{ route('sidlak.index') }}" class="btn btn-link p-0 text-decoration-none">Reset filters</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($journals as $journal)
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('sidlak.show', $journal->id) }}" class="text-decoration-none sidlak-card-link">
                    <div class="card h-100 shadow-lg border-0 rounded-4 bg-white position-relative overflow-hidden sidlak-card-hover">
                        <div class="position-relative">
                            <img src="{{ $journal->cover_photo ? asset('storage/' . $journal->cover_photo) : asset('images/placeholder.jpg') }}" class="card-img-top rounded-top-4" style="height: 260px; object-fit: cover; border-bottom: 4px solid #e83e8c;" alt="Cover">
                            <span class="badge position-absolute top-0 end-0 m-3 px-3 py-2 fs-6 shadow" style="background:#e83e8c;">{{ $journal->month }} {{ $journal->year }}</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-1" style="font-size:1.25rem;color:#e83e8c;">{{ $journal->title }}</h5>
                            <div class="mb-2 text-muted small">ISSN: <span class="fw-semibold">{{ $journal->print_issn }}</span></div>
                            <p class="card-text flex-grow-1">{{ Str::limit($journal->description, 120) }}</p>
                            <span class="btn mt-2 align-self-end rounded-pill px-4" style="border:1.5px solid #e83e8c;color:#e83e8c;pointer-events:none;">View Details</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">No SIDLAK journals matched your search/filter criteria.</div>
            </div>
        @endforelse
    </div>

    @if(method_exists($journals, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $journals->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
<div style="margin-bottom: 120px;"></div>
@include('footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
