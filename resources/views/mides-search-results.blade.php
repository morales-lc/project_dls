@include('navbar')
<div class="container py-4">
    <h2 class="fw-bold mb-3">Search Results</h2>
    <div class="mb-4">
        <form class="d-flex" method="GET" action="{{ route('mides.search') }}">
            <input class="form-control me-2" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search again..." aria-label="Search">
            <select class="form-select me-2" name="type">
                <option value="">SELECT TYPE</option>
                @if(isset($types))
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                @endif
            </select>
            <button class="btn btn-dark" type="submit">Search</button>
        </form>
    </div>
    @if($documents->count())
        <div class="row g-4">
            @foreach($documents as $doc)
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $doc->title }}</h5>
                            <div class="mb-2"><span class="text-muted">Author:</span> {{ $doc->author }}</div>
                            <div class="mb-2"><span class="text-muted">Year:</span> {{ $doc->year }}</div>
                            <div class="mb-2"><span class="text-muted">Type:</span> {{ $doc->type }}</div>
                            @if($doc->category)
                                <div class="mb-2"><span class="text-muted">Program:</span> {{ $doc->category }}</div>
                            @endif
                            @if($doc->program)
                                <div class="mb-2"><span class="text-muted">Program:</span> {{ $doc->program }}</div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-0 d-flex gap-2">
                            <a href="{{ asset('storage/' . $doc->pdf_path) }}" target="_blank" class="btn btn-outline-primary flex-fill">View PDF</a>
                            <a href="{{ asset('storage/' . $doc->pdf_path) }}" download="{{ basename($doc->pdf_path) }}" class="btn btn-success flex-fill">Download PDF</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    @else
        <div class="alert alert-info">No results found for your search.</div>
    @endif
    <div class="mt-4 text-center">
        <a href="{{ route('mides.dashboard') }}" class="btn btn-secondary">Back to MIDES Dashboard</a>
    </div>
</div>

