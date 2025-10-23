@include('navbar')

<style>
    /* ---------- Base ---------- */
    body {
        background: #f8f9fb;
        color: #333;
        font-family: "Inter", system-ui, sans-serif;
    }

    .container {
        max-width: 1200px;
    }

    /* ---------- View Toggle ---------- */
    .view-toggle button {
        border: none;
        background: #fff;
        font-size: 1.25rem;
        color: #666;
        padding: .4rem .6rem;
        border-radius: .5rem;
        transition: all .25s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
    }

    .view-toggle button.active,
    .view-toggle button:hover {
        background: #0d6efd;
        color: #fff;
        transform: translateY(-2px);
    }

    /* ---------- Catalog Styles ---------- */
    .catalog-list .catalog-item,
    .catalog-grid .catalog-item {
        position: relative;
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: .75rem;
        overflow: hidden;
        transition: all .25s ease;
        cursor: pointer;
    }

    .catalog-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
    }

    /* ---------- Animation ---------- */
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(8px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ---------- Grid View ---------- */
    .catalog-grid {
        display: grid;
        gap: 1.25rem;
        grid-template-columns: repeat(1, 1fr);
        animation: fadeInUp .3s ease;
    }

    @media (min-width: 576px) {
        .catalog-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .catalog-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1200px) {
        .catalog-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .catalog-grid .catalog-item {
        display: flex;
        flex-direction: column;
        padding: 1rem;
    }

    .catalog-grid .catalog-thumb img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        border-radius: .5rem;
        transition: transform .3s ease;
    }

    .catalog-grid .catalog-item:hover .catalog-thumb img {
        transform: scale(1.05);
    }

    /* ---------- List View ---------- */
    .catalog-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        animation: fadeInUp .3s ease;
    }

    .catalog-list .catalog-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        padding: 1rem;
    }

    .catalog-list .catalog-thumb img {
        width: 120px;
        height: 160px;
        object-fit: cover;
        border-radius: .5rem;
    }

    /* ---------- Catalog Info ---------- */
    .catalog-info h6 {
        font-weight: 600;
        color: #212529;
        margin-bottom: .25rem;
    }

    .catalog-info p {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 0.3rem;
    }

    .catalog-info .subjects {
        font-size: 0.85rem;
        color: #666;
    }

    .catalog-info .clamp {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ---------- Actions ---------- */
    .catalog-actions {
        position: absolute;
        top: .5rem;
        right: .5rem;
    }

    .catalog-actions a {
        font-size: 0.9rem;
        color: #0d6efd;
        text-decoration: none;
        transition: all .2s ease;
    }

    .catalog-actions a:hover {
        color: #084298;
        text-decoration: underline;
    }

    /* ---------- MOBILE STRIP VIEW (Updated for real list strip look) ---------- */
    @media (max-width: 768px) {
        .catalog-list .catalog-item {
            display: grid;
            grid-template-columns: 70px 1fr auto;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1rem;
            border-radius: .6rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .catalog-list .catalog-thumb img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: .4rem;
        }

        .catalog-list .catalog-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .catalog-list .catalog-info h6 {
            font-size: .9rem;
            margin-bottom: .1rem;
            font-weight: 600;
            color: #111;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .catalog-list .catalog-info p {
            font-size: .78rem;
            color: #555;
            margin-bottom: .1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .catalog-list .catalog-actions {
            position: static;
            text-align: right;
        }

        .catalog-list .catalog-actions a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: .5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
            color: #444;
            font-size: 1rem;
        }

        .catalog-list .catalog-actions a:hover {
            background: #f3f3f3;
            color: #0d6efd;
            border-color: #ccc;
        }
    }

    /* ---------- Pagination Visibility Fix ---------- */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }

    .page-item {
        margin: 2px;
    }

    .pagination .page-link {
        padding: 0.4rem 0.7rem;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    @media (max-width: 768px) {
        .text-muted.small {
            text-align: center;
            font-size: 0.85rem;
        }

        .pagination {
            justify-content: center !important;
            margin-top: 0.5rem;
        }
    }

    /* ---------- Search Button Hover (Dark Pink) ---------- */
    .btn-primary {
        background-color: #e83e8c;
        /* dark pink base */
        border-color: #e83e8c;
        font-weight: 700;
        /* bold white text */
        color: #fff;
        transition: all 0.25s ease;
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background-color: #c71f6e;
        /* darker pink on hover */
        border-color: #c71f6e;
        color: #fff;
        transform: translateY(-1px);
    }

    /* ---------- Search Button Active/Pressed ---------- */
    .btn-primary:active {
        background-color: #a51a5c !important;
        border-color: #a51a5c !important;
    }

    /* ---------- Library Catalog Title ---------- */
    h4.text-primary {
        font-weight: 800;
        /* thicker title text */
        color: #0d6efd !important;
        letter-spacing: 0.3px;
    }
</style>

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
    <div class="mb-3 text-muted small">
        Showing <strong>{{ $catalogs->firstItem() }}</strong>–<strong>{{ $catalogs->lastItem() }}</strong> of <strong>{{ $catalogs->total() }}</strong> results
    </div>
    @else
    <div class="mb-3 text-muted small">No results found.</div>
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