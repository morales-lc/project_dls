<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIDES Search Results</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #fafafa;
        }

        .view-toggle {
            text-align: right;
        }

        .view-toggle .btn {
            border-radius: 8px;
            transition: all .2s ease-in-out;
        }

        .view-toggle .btn.active {
            background-color: #e83e8c;
            color: #fff;
            border-color: #e83e8c;
        }

        .card-footer .btn {
            min-width: 110px;
            transition: all 0.2s ease-in-out;
        }

        .card-footer .btn:hover {
            transform: translateY(-2px);
        }

        .card.h-100 {
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
            border-radius: 0.85rem;
            overflow: hidden;
            border: 2px solid #e0e0e0;
            background-color: #fff;
        }

        .card.h-100:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 10px 30px rgba(216, 27, 96, 0.14), 0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: #d81b60;
        }

        .btn-outline-warning {
            border-color: #ffd1e3 !important;
            color: #d81b60 !important;
            background: white;
        }

        .btn-outline-warning:hover {
            background: #ffd1e3 !important;
            color: #8b0f3a !important;
        }

        .btn-pink {
            background: #e83e8c !important;
            color: #fff !important;
            border: none !important;
        }

        .card-body {
            background: linear-gradient(180deg, #fff 92%, #fff7fb 100%);
        }

        .list-view .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .list-view .card {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }

        .list-view .card-body {
            flex: 1;
        }

        .list-view .card-footer {
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .no-results {
            padding: 2rem;
            background: #fff;
            border-radius: 0.75rem;
            border: 1px solid #e0e0e0;
            color: #555;
        }
    </style>
</head>

<body>
    @include('navbar')

    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-2 mb-md-0">Search Results</h2>

            <div class="view-toggle">
                <button class="btn btn-outline-secondary btn-sm active" id="grid-view-btn">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Grid
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="list-view-btn">
                    <i class="bi bi-list-ul"></i> List
                </button>
            </div>
        </div>

        <div class="mb-3 text-center">
            <a href="{{ route('mides.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
            </a>
        </div>

        <!-- Search Form -->
        <form class="row g-2 mb-4" method="GET" action="{{ route('mides.search') }}">
            <div class="col-12 col-md-5">
                <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search again..." aria-label="Search">
            </div>
            <div class="col-12 col-md-3">
                <select class="form-select" name="type" id="mides-type-select-2">
                    <option value="">SELECT TYPE</option>
                    @foreach($types as $t)
                    <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3" id="mides-program-col-2" style="display:none; min-width:180px;">
                <select class="form-select" name="program" id="mides-program-select-2">
                    <option value="">SELECT PROGRAM</option>
                    @foreach($programs as $p)
                    <option value="{{ $p }}" {{ (isset($program) && $program == $p) ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <select class="form-select" name="year">
                    <option value="">SELECT YEAR</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ (request('year') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-1 d-grid">
                <button class="btn btn-dark" type="submit">Search</button>
            </div>
        </form>

        @if($documents->count())
        <div id="search-results" class="row g-4 grid-view">
            @foreach($documents as $doc)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-truncate" title="{{ $doc->title }}">
                            {{ $doc->title }}
                        </h5>
                        <div class="small mb-2"><span class="text-muted">Author:</span> {{ $doc->author }}</div>
                        <div class="small mb-2"><span class="text-muted">Year:</span> {{ $doc->year }}</div>
                        <div class="small mb-2"><span class="text-muted">Type:</span> {{ $doc->type }}</div>
                        @if($doc->category)
                        <div class="small mb-2"><span class="text-muted">Category:</span> {{ $doc->category }}</div>
                        @endif
                        @if($doc->program)
                        <div class="small mb-2"><span class="text-muted">Program:</span> {{ $doc->program }}</div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center px-3 py-2">
                        <a href="{{ route('mides.search.viewer', $doc->id) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"
                            style="border-radius: 8px;">
                            <i class="bi bi-box-arrow-up-right"></i> Open
                        </a>

                        @if(Auth::check() && Auth::user()->role !== 'guest')
                        @php
                        $sf = optional(auth()->user()->studentFaculty);
                        $isBookmarked = $sf && $sf->id
                        ? \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                        ->where('bookmarkable_type', \App\Models\MidesDocument::class)
                        ->where('bookmarkable_id', $doc->id)
                        ->exists()
                        : false;
                        @endphp
                        @if($sf && $sf->id)
                        <form method="POST" action="{{ route('bookmarks.toggle') }}" class="bookmark-toggle m-0">
                            @csrf
                            <input type="hidden" name="id" value="{{ $doc->id }}">
                            <input type="hidden" name="type" value="mides">
                            <button type="submit"
                                class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} d-flex align-items-center gap-1 bookmark-btn"
                                style="border-radius: 8px;">
                                <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                                <span class="ms-1">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                            </button>
                        </form>
                        @endif
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $documents->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        @else
        <div class="no-results text-center">
            <i class="bi bi-search fs-3 text-secondary"></i>
            <p class="mt-2 mb-0">No results found for your search.</p>
        </div>
        @endif
    </div>

    <div style="height: 120px;"></div>
    @include('footer')

    <script>
        // Toggle between Grid and List views
        document.addEventListener('DOMContentLoaded', function() {
            const gridBtn = document.getElementById('grid-view-btn');
            const listBtn = document.getElementById('list-view-btn');
            const container = document.getElementById('search-results');

            gridBtn.addEventListener('click', function() {
                container.classList.remove('list-view');
                container.classList.add('grid-view');
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
            });

            listBtn.addEventListener('click', function() {
                container.classList.remove('grid-view');
                container.classList.add('list-view');
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
            });
        });
        
        // AJAX bookmark toggle (prevent page reload)
        document.querySelectorAll('.bookmark-toggle').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('button[type="submit"]');
                if (!btn) return;
                var originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                var formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                    },
                    body: formData
                }).then(function(res) { return res.json(); }).then(function(data) {
                    if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                        var icon = btn.querySelector('i');
                        var text = btn.querySelector('span');
                        if (icon) {
                            icon.className = data.status === 'bookmarked' ? 'bi bi-bookmark-fill' : 'bi bi-bookmark';
                        }
                        if (text) {
                            text.textContent = data.status === 'bookmarked' ? 'Bookmarked' : 'Bookmark';
                        }
                        if (data.status === 'bookmarked') btn.classList.add('btn-pink'); else btn.classList.remove('btn-pink');
                        if (data.status === 'bookmarked') btn.classList.remove('btn-outline-warning'); else btn.classList.add('btn-outline-warning');
                    } else {
                        alert((data && data.message) || 'Unexpected response');
                    }
                }).catch(function(err) {
                    console.error(err);
                    alert('Failed to update bookmark.');
                }).finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
            });
            // prevent parent card clicks from being triggered when interacting with bookmark
            form.addEventListener('click', function(ev){ ev.stopPropagation(); });
        });
    </script>
</body>

</html>