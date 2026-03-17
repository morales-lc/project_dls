@include('navbar')
<link href="{{ asset('css/catalog-search.css') }}" rel="stylesheet">


<head>
    <title>Catalog Search</title>
</head>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h4 class="mb-2 mb-md-0 text-primary">📚 Library Catalog</h4>
        <div class="view-toggle">
            <button id="listViewBtn" class="active" title="List View"><i class="bi bi-list"></i></button>
            <button id="gridViewBtn" title="Grid View"><i class="bi bi-grid-3x3-gap-fill"></i></button>
        </div>
    </div>

    <!-- Search -->
    <form class="search-bar d-flex flex-nowrap align-items-center gap-2 flex-wrap"
        method="GET"
        action="{{ route('catalogs.search') }}"
        style="max-width: 1600px;">
        <div class="input-group flex-grow-1" style="min-width: 250px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                placeholder="Search by keyword, title, author, ISBN, ISSN, or LCCN...">
        </div>
        <button type="submit"
            class="btn btn-pink"
            style="
            background-color: #e83e8c; 
            color: white; 
            border: none; 
            padding: 0.55rem 1.25rem; 
            border-radius: 8px;
            white-space: nowrap;
            transition: 0.3s;
        "
            onmouseover="this.style.backgroundColor='#d63384';"
            onmouseout="this.style.backgroundColor='#e83e8c';">
            Search
        </button>
        <div class="form-check ms-2" title="Require all words to match">
            <input class="form-check-input" type="checkbox" value="and" id="modeAnd"
                   name="mode" {{ request('mode') === 'and' ? 'checked' : '' }}>
            <label class="form-check-label small" for="modeAnd">Match all words</label>
        </div>
    </form>

    @if($catalogs->total() > 0)
    <div class="mb-3 mt-3 text-muted small">
        Showing <strong>{{ $catalogs->firstItem() }}</strong>–<strong>{{ $catalogs->lastItem() }}</strong> of <strong>{{ $catalogs->total() }}</strong> results
    </div>
    @else
    <div class="mt-5 mb-5 text-center">
        <div class="alert alert-light border shadow-sm p-5" style="max-width: 600px; margin: 0 auto; border-radius: 1rem;">
            <i class="bi bi-search" style="font-size: 4rem; color: #e83e8c; opacity: 0.3;"></i>
            <h5 class="mt-3 mb-2 fw-bold text-secondary">No Results Found</h5>
            <p class="text-muted mb-0">We couldn't find any items matching your search. Try different keywords or check your spelling.</p>
        </div>
    </div>
    @endif

    <!-- Catalog Container -->
    <div id="catalogContainer" class="catalog-list">
        @foreach($catalogs as $catalog)
        <div class="catalog-item">
            <div class="catalog-thumb">
                <img src="{{ asset('images/book-placeholder.png') }}" alt="Cover">
            </div>

            <div class="catalog-info">
                <h6>{{ $catalog->title }}</h6>
                @if($catalog->author)
                <p>{{ $catalog->author }}</p>
                @endif
                @if($catalog->publisher)
                <p>{{ $catalog->publisher }}</p>
                @endif
            </div>

            <div class="catalog-actions">
                <a href="#"><i class="bi bi-bookmark-plus me-1"></i></a>
            </div>

            <a href="{{ route('catalogs.show', $catalog->id) }}" class="stretched-link"></a>
        </div>
        @endforeach
    </div>

    @if($catalogs->hasPages())
    <div class="mt-4">{{ $catalogs->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const listBtn = document.getElementById('listViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        const container = document.getElementById('catalogContainer');

        // Restore last view mode from localStorage (default to list)
        const savedView = localStorage.getItem('catalogView') || 'list';
        if (savedView === 'grid') {
            container.classList.replace('catalog-list', 'catalog-grid');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        }

        // When clicking List View
        listBtn.addEventListener('click', () => {
            container.classList.replace('catalog-grid', 'catalog-list');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            localStorage.setItem('catalogView', 'list');
        });

        // When clicking Grid View
        gridBtn.addEventListener('click', () => {
            container.classList.replace('catalog-list', 'catalog-grid');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            localStorage.setItem('catalogView', 'grid');
        });
    });
</script>


@include('footer')