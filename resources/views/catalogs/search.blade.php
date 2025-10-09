@include('navbar')

<style>
/* Search bar layout */
.search-box {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}
.search-box .form-control,
.search-box .form-select {
    border-color: #ccc;
}
.search-box .btn {
    background-color: #ff4081;
    border: none;
    color: white;
}
.search-box .btn:hover {
    background-color: #e73573;
}

/* Results view modes */
.catalog-result-list,
.catalog-result-grid {
    border-top: 1px solid #e0e0e0;
}
.catalog-item {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e0e0e0;
    position: relative;
    cursor: pointer;
    transition: background 0.15s ease-in-out;
}
.catalog-item:hover {
    background: #fff6f9;
}
.catalog-item:last-child { border-bottom: none; }

.catalog-thumb {
    flex: 0 0 110px;
    height: 140px;
    background: #f8f9fa;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.catalog-thumb img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.catalog-info { flex: 1; min-width: 220px; }
.catalog-info h6 { font-weight: 700; font-size: 1.05rem; }
.catalog-info p { font-size: 0.9rem; color: #555; }
.catalog-meta { font-size: 0.9rem; color: #555; }
.catalog-actions a { color: #333; font-weight: 600; text-decoration: none; z-index: 2; position: relative; }
.catalog-actions a:hover { color: #ff4081; }

/* Grid layout */
.catalog-result-grid .catalog-item {
    flex-direction: column;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}
.catalog-result-grid .catalog-thumb {
    height: 180px;
    width: 100%;
}
.catalog-result-grid .catalog-info {
    flex: none;
}
.catalog-result-grid .catalog-meta {
    font-size: 0.85rem;
}

/* Toggle buttons */
.view-toggle {
    display: flex;
    gap: 0.4rem;
}
.view-toggle button {
    border: 1px solid #ccc;
    background: #fff;
    color: #333;
    border-radius: 4px;
    padding: 6px 10px;
}
.view-toggle button.active {
    background-color: #ff4081;
    border-color: #ff4081;
    color: #fff;
}
.view-toggle button i {
    font-size: 1rem;
}

/* Responsive */
@media (max-width: 767px) {
    .catalog-item { flex-direction: column; }
    .catalog-thumb { width: 100%; height: 180px; }
    .catalog-actions { margin-top: 0.5rem; }
}
</style>

<div class="container py-4">
    <!-- Search Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h4 class="mb-2 mb-md-0">Library Catalog</h4>
        <div class="view-toggle">
            <button type="button" id="listViewBtn" class="active" title="List View"><i class="bi bi-list"></i></button>
            <button type="button" id="gridViewBtn" title="Grid View"><i class="bi bi-grid-3x3-gap-fill"></i></button>
        </div>
    </div>

    <!-- Search Bar -->
    <form class="d-flex gap-2 flex-wrap search-box" method="GET" action="{{ route('catalogs.search') }}">
        <div class="input-group flex-grow-1" style="min-width:200px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Search books, journals, authors...">
        </div>
        <select name="type" class="form-select" style="min-width:160px; max-width:220px;">
            <option value="">SELECT TYPE</option>
            <option value="Book" {{ request('type')=='Book' ? 'selected' : '' }}>Book</option>
            <option value="eBook" {{ request('type')=='eBook' ? 'selected' : '' }}>eBook</option>
            <option value="Journal" {{ request('type')=='Journal' ? 'selected' : '' }}>Journal</option>
            <option value="Thesis" {{ request('type')=='Thesis' ? 'selected' : '' }}>Thesis</option>
            <option value="Dissertation" {{ request('type')=='Dissertation' ? 'selected' : '' }}>Dissertation</option>
            <option value="Map" {{ request('type')=='Map' ? 'selected' : '' }}>Map</option>
            <option value="Multimedia" {{ request('type')=='Multimedia' ? 'selected' : '' }}>Multimedia</option>
        </select>
        <button type="submit" class="btn">Search</button>
    </form>

    <!-- Results Info -->
    @if($catalogs->total() > 0)
        <div class="mb-3 text-muted small">
            Showing <strong>{{ $catalogs->firstItem() }}</strong>–<strong>{{ $catalogs->lastItem() }}</strong> of <strong>{{ $catalogs->total() }}</strong> results
        </div>
    @else
        <div class="mb-3 text-muted small">No results found.</div>
    @endif

    <!-- Search Results -->
    <div id="resultList" class="catalog-result-list">
        @forelse($catalogs as $catalog)
            <div class="catalog-item">
                <div class="catalog-thumb">
                    <img src="{{ asset('images/book-placeholder.png') }}" alt="Cover">
                </div>
                <div class="catalog-info">
                    <h6>{{ $catalog->title }}</h6>
                    @if(!empty($catalog->additional_info))
                        <p>{{ Str::limit($catalog->additional_info, 140) }}</p>
                    @endif
                    <div class="catalog-meta">
                        @if($catalog->author)
                            <div>Author: {{ $catalog->author }}</div>
                        @endif
                        @if($catalog->year)
                            <div>Year: {{ $catalog->year }}</div>
                        @endif
                        @if($catalog->sublocation)
                            <div>Location: {{ $catalog->sublocation }}</div>
                        @endif
                    </div>
                </div>
                <div class="catalog-actions align-self-start">
                    <a href="#"><i class="bi bi-plus-circle me-1"></i>Add to list</a>
                </div>
                <a href="{{ route('catalogs.show', $catalog->id) }}" class="stretched-link"></a>
            </div>
        @empty
            <p class="text-muted">No results found for your search.</p>
        @endforelse
    </div>

    @if($catalogs->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $catalogs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const listBtn = document.getElementById('listViewBtn');
    const gridBtn = document.getElementById('gridViewBtn');
    const resultList = document.getElementById('resultList');

    listBtn.addEventListener('click', () => {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        resultList.classList.remove('catalog-result-grid');
        resultList.classList.add('catalog-result-list');
    });

    gridBtn.addEventListener('click', () => {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        resultList.classList.remove('catalog-result-list');
        resultList.classList.add('catalog-result-grid');
    });
});
</script>

@include('footer')
