@include('navbar')

<style>
/* Local styles for catalog index */
.catalog-grid .card { min-height:160px; }
.catalog-grid .card-title { font-size:1rem; font-weight:700; }
@media (max-width:767px) {
    .catalog-grid .card { min-height:140px; }
}
/* stronger typography and input border styles (index) */
.catalogs-typo { font-weight:600; }
.catalog-grid .card { border-width: 1.2px; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Library Catalog</h4>
        <a href="{{ route('catalogs.create') }}" class="btn btn-pink">Add Catalog Item</a>
    </div>

    <div class="row g-3 catalog-grid">
        @foreach($catalogs as $catalog)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate">{{ $catalog->title }}</h5>
                        @if(!empty($catalog->author))
                            <p class="text-muted small mb-2">{{ $catalog->author }}</p>
                        @endif
                        @if(!empty($catalog->publisher))
                            <p class="flex-grow-1 small text-muted">{{ $catalog->publisher }} ({{ $catalog->year }})</p>
                        @endif
                        <div class="mt-2 d-flex gap-2">
                            <a href="{{ route('catalogs.show', $catalog->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($catalogs->hasPages())
        <div class="mt-4 d-flex justify-content-center" aria-label="Catalog list pagination">
            {{ $catalogs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@include('footer')
