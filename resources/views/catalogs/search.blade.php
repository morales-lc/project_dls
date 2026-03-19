@include('navbar')
<link href="{{ asset('css/catalog-search.css') }}" rel="stylesheet">


<head>
    <title>Catalog Search</title>
    <style>
        .search-results-title {
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #1f2937;
        }

        #searchResultTabs .nav-link {
            font-weight: 700;
            color: #334155;
        }

        #searchResultTabs .nav-link.active {
            font-weight: 800;
            color: #111827;
        }

        .catalog-info h6 {
            font-weight: 700;
            color: #111827;
            line-height: 1.35;
        }

        .catalog-info p,
        .catalog-info .text-muted,
        .result-summary {
            font-weight: 600;
            color: #374151 !important;
        }

        @media (max-width: 575.98px) {
            .search-results-title {
                font-size: 1.15rem;
            }

            #searchResultTabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            #searchResultTabs .nav-item {
                flex: 0 0 auto;
            }

            #searchResultTabs .nav-link {
                font-size: 0.9rem;
                padding: 0.55rem 0.7rem;
                white-space: nowrap;
            }

            .catalog-info h6 {
                font-size: 1rem;
            }

            .catalog-info p,
            .catalog-info .text-muted,
            .result-summary {
                font-size: 0.93rem;
                line-height: 1.4;
            }
        }
    </style>
</head>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h4 class="mb-2 mb-md-0 text-primary search-results-title">🔍 Search Results</h4>
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

    @php
        $hasAnyResults = $catalogs->total() > 0
            || $midesDocuments->isNotEmpty()
            || $sidlakJournals->isNotEmpty()
            || $sidlakArticles->isNotEmpty();

        // Determine which tab should be active by default (first one with results)
        $activeTab = 'catalogs';
        if ($catalogs->total() > 0) {
            $activeTab = 'catalogs';
        } elseif ($midesDocuments->isNotEmpty()) {
            $activeTab = 'mides';
        } elseif ($sidlakJournals->isNotEmpty()) {
            $activeTab = 'sidlak-journals';
        } elseif ($sidlakArticles->isNotEmpty()) {
            $activeTab = 'sidlak-articles';
        }
    @endphp

    @if(request('q') && !$hasAnyResults)
    <div class="mt-5 mb-5 text-center">
        <div class="alert alert-light border shadow-sm p-5" style="max-width: 600px; margin: 0 auto; border-radius: 1rem;">
            <i class="bi bi-search" style="font-size: 4rem; color: #e83e8c; opacity: 0.3;"></i>
            <h5 class="mt-3 mb-2 fw-bold text-secondary">No Results Found</h5>
            <p class="text-muted mb-0">We couldn't find any items matching your search. Try different keywords or check your spelling.</p>
        </div>
    </div>
    @endif

    @if($hasAnyResults)
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mt-4" id="searchResultTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'catalogs' ? 'active' : '' }}"
                id="tab-catalogs" data-bs-toggle="tab" data-bs-target="#pane-catalogs"
                type="button" role="tab">
                <i class="bi bi-collection me-1"></i>Catalog
                <span class="badge ms-1 {{ $catalogs->total() > 0 ? 'bg-pink' : 'bg-secondary' }}" style="{{ $catalogs->total() > 0 ? 'background-color:#e83e8c!important;' : '' }}">
                    {{ $catalogs->total() }}
                </span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'mides' ? 'active' : '' }}"
                id="tab-mides" data-bs-toggle="tab" data-bs-target="#pane-mides"
                type="button" role="tab">
                <i class="bi bi-file-earmark-text me-1"></i>MIDES Documents
                <span class="badge ms-1 {{ $midesDocuments->isNotEmpty() ? 'bg-pink' : 'bg-secondary' }}" style="{{ $midesDocuments->isNotEmpty() ? 'background-color:#e83e8c!important;' : '' }}">
                    {{ $midesDocuments->count() }}
                </span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'sidlak-journals' ? 'active' : '' }}"
                id="tab-sidlak-journals" data-bs-toggle="tab" data-bs-target="#pane-sidlak-journals"
                type="button" role="tab">
                <i class="bi bi-journal-richtext me-1"></i>SIDLAK Journals
                <span class="badge ms-1 {{ $sidlakJournals->isNotEmpty() ? 'bg-primary' : 'bg-secondary' }}">
                    {{ $sidlakJournals->count() }}
                </span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'sidlak-articles' ? 'active' : '' }}"
                id="tab-sidlak-articles" data-bs-toggle="tab" data-bs-target="#pane-sidlak-articles"
                type="button" role="tab">
                <i class="bi bi-file-earmark-richtext me-1"></i>SIDLAK Articles
                <span class="badge ms-1 {{ $sidlakArticles->isNotEmpty() ? 'bg-primary' : 'bg-secondary' }}">
                    {{ $sidlakArticles->count() }}
                </span>
            </button>
        </li>
    </ul>

    <!-- Tab Panes -->
    <div class="tab-content mt-3" id="searchResultTabsContent">

        <!-- Catalog Tab -->
        <div class="tab-pane fade {{ $activeTab === 'catalogs' ? 'show active' : '' }}"
             id="pane-catalogs" role="tabpanel">
            @if($catalogs->isNotEmpty())
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small result-summary">Showing {{ $catalogs->firstItem() }}–{{ $catalogs->lastItem() }} of {{ $catalogs->total() }} results</span>
            </div>
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
            @else
            <div class="py-5 text-center text-muted">
                <i class="bi bi-collection" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-2">No catalog items found for this search.</p>
            </div>
            @endif
        </div>

        <!-- MIDES Documents Tab -->
        <div class="tab-pane fade {{ $activeTab === 'mides' ? 'show active' : '' }}"
             id="pane-mides" role="tabpanel">
            @if($midesDocuments->isNotEmpty())
            <div class="catalog-list">
                @foreach($midesDocuments as $doc)
                <div class="catalog-item">
                    <div class="catalog-thumb">
                        <img src="{{ asset('images/book-placeholder.png') }}" alt="Document">
                    </div>
                    <div class="catalog-info">
                        <h6>{{ $doc->title }}</h6>
                        @if($doc->author)
                        <p>{{ $doc->author }}</p>
                        @endif
                        <p class="text-muted small">{{ implode(' · ', array_filter([$doc->type, $doc->year])) }}</p>
                    </div>
                    <div class="catalog-actions">
                        @if(auth()->check())
                        <a href="{{ route('mides.search.viewer', $doc->id) }}" target="_blank"><i class="bi bi-eye"></i></a>
                        @endif
                    </div>
                    @if(auth()->check())
                    <a href="{{ route('mides.search.viewer', $doc->id) }}" class="stretched-link"></a>
                    @else
                    <a href="{{ route('login') }}" class="stretched-link" title="Login required to view"></a>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="py-5 text-center text-muted">
                <i class="bi bi-file-earmark-text" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-2">No MIDES documents found for this search.</p>
            </div>
            @endif
        </div>

        <!-- SIDLAK Journals Tab -->
        <div class="tab-pane fade {{ $activeTab === 'sidlak-journals' ? 'show active' : '' }}"
             id="pane-sidlak-journals" role="tabpanel">
            @if($sidlakJournals->isNotEmpty())
            <div class="catalog-list">
                @foreach($sidlakJournals as $journal)
                <div class="catalog-item">
                    <div class="catalog-thumb">
                        @if($journal->cover_photo)
                        <img src="{{ asset('storage/' . $journal->cover_photo) }}" alt="Cover">
                        @else
                        <img src="{{ asset('images/book-placeholder.png') }}" alt="Journal">
                        @endif
                    </div>
                    <div class="catalog-info">
                        <h6>{{ $journal->title }}</h6>
                        @if($journal->month && $journal->year)
                        <p>{{ $journal->month }} {{ $journal->year }}</p>
                        @endif
                        @if($journal->print_issn)
                        <p class="text-muted small">ISSN: {{ $journal->print_issn }}</p>
                        @endif
                    </div>
                    <div class="catalog-actions">
                        @if(auth()->check())
                        <a href="{{ route('sidlak.show', $journal->id) }}"><i class="bi bi-eye"></i></a>
                        @endif
                    </div>
                    @if(auth()->check())
                    <a href="{{ route('sidlak.show', $journal->id) }}" class="stretched-link"></a>
                    @else
                    <a href="{{ route('login') }}" class="stretched-link" title="Login required to view"></a>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="py-5 text-center text-muted">
                <i class="bi bi-journal-richtext" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-2">No SIDLAK journals found for this search.</p>
            </div>
            @endif
        </div>

        <!-- SIDLAK Articles Tab -->
        <div class="tab-pane fade {{ $activeTab === 'sidlak-articles' ? 'show active' : '' }}"
             id="pane-sidlak-articles" role="tabpanel">
            @if($sidlakArticles->isNotEmpty())
            <div class="catalog-list">
                @foreach($sidlakArticles as $article)
                <div class="catalog-item">
                    <div class="catalog-thumb">
                        <img src="{{ asset('images/book-placeholder.png') }}" alt="Article">
                    </div>
                    <div class="catalog-info">
                        <h6>{{ $article->title }}</h6>
                        @if($article->authors)
                        <p>{{ $article->authors }}</p>
                        @endif
                        @if($article->journal)
                        <p class="text-muted small">{{ $article->journal->title }}{{ $article->journal->year ? ' (' . $article->journal->year . ')' : '' }}</p>
                        @endif
                    </div>
                    <div class="catalog-actions">
                        @if(auth()->check())
                        <a href="{{ route('sidlak.article.download', $article->id) }}"><i class="bi bi-download"></i></a>
                        @endif
                    </div>
                    @if(auth()->check())
                    <a href="{{ route('sidlak.article.download', $article->id) }}" class="stretched-link"></a>
                    @else
                    <a href="{{ route('login') }}" class="stretched-link" title="Login required to download"></a>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="py-5 text-center text-muted">
                <i class="bi bi-file-earmark-richtext" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-2">No SIDLAK articles found for this search.</p>
            </div>
            @endif
        </div>

    </div><!-- /.tab-content -->
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const listBtn = document.getElementById('listViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        const container = document.getElementById('catalogContainer');

        if (!container) return;

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