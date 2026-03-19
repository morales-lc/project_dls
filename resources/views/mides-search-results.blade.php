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
            padding: 3rem 2rem;
            background: #fff;
            border-radius: 0.75rem;
            border: 1px solid #e0e0e0;
            color: #555;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .results-container {
            min-height: calc(100vh - 600px);
        }

        .search-shell {
            border: 1px solid #f5cada;
            border-radius: 1rem;
            padding: 1rem;
            background: linear-gradient(140deg, #fff 0%, #fff5fa 100%);
            box-shadow: 0 10px 26px rgba(232, 62, 140, 0.12);
        }

        .tag-discovery-card {
            margin-top: .85rem;
            padding: .85rem;
            border-radius: .95rem;
            border: 2px solid #f28bb8;
            background: linear-gradient(145deg, #fff7fc 0%, #fff 100%);
            box-shadow: 0 6px 20px rgba(232, 62, 140, 0.14);
        }

        .tag-toggle-btn {
            border: none;
            color: #fff;
            background: #e83e8c;
            font-weight: 700;
            border-radius: 999px;
            padding: .4rem .9rem;
        }

        .tag-filter-box {
            display: block;
            margin-top: .75rem;
            padding: .75rem;
            border-radius: .9rem;
            border: 1px solid #f4bfd5;
            background: #fff;
        }

        .suggested-tags {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .suggested-tag-btn {
            border: 1px solid #f2a8ca;
            background: #fff;
            color: #9a1e5d;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            padding: .2rem .65rem;
        }

        .suggested-tag-btn:hover {
            background: #ffe2ef;
        }

        .tag-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            background: #ffd9e9;
            color: #7a0f42;
            font-size: .85rem;
            font-weight: 600;
            margin: .2rem;
        }

        .tag-chip button {
            border: none;
            background: transparent;
            color: #7a0f42;
            font-size: .95rem;
            line-height: 1;
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
        <form class="mb-4 search-shell" method="GET" action="{{ route('mides.search') }}" id="midesSearchResultForm">
            <div class="row g-2 mb-2">
                <div class="col-12 col-md-8">
                    <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search again..." aria-label="Search">
                </div>
                <div class="col-12 col-md-4">
                    <button class="btn btn-dark w-100" type="submit">Search</button>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <select class="form-select" name="type" id="mides-type-select-2">
                        <option value="">SELECT TYPE</option>
                        @foreach($types as $t)
                        <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4" id="mides-program-col-2" style="display:{{ (isset($type) && $type && !str_contains(strtolower($type), 'faculty') && !str_contains(strtolower($type), 'dissertation')) ? 'block' : 'none' }};">
                    <select class="form-select" name="program" id="mides-program-select-2">
                        <option value="">SELECT PROGRAM</option>
                        @foreach($programs as $p)
                        <option value="{{ $p }}" {{ (isset($program) && $program == $p) ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <input
                        type="date"
                        class="form-control"
                        name="publication_date"
                        value="{{ request('publication_date') ?? '' }}"
                        max="{{ date('Y-m-d') }}"
                        aria-label="Filter by publication date"
                    >
                </div>
            </div>

            <div class="tag-discovery-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="fw-bold" style="color:#a31358;"><i class="bi bi-stars"></i> Tag Discovery</div>
                        <div class="small text-muted">Use tags to discover related theses and research quickly.</div>
                    </div>
                    <button type="button" class="btn tag-toggle-btn" id="toggleTagSearch2">
                        <i class="bi bi-tags"></i> Hide Tag Search
                    </button>
                </div>
                <div id="tagFilterBox2" class="tag-filter-box">
                    <label for="tagInput2" class="form-label fw-semibold mb-1">Type a tag and press Enter</label>
                    <input type="text" id="tagInput2" class="form-control" list="midesTagSuggestions2" placeholder="e.g. educational technology">
                    <datalist id="midesTagSuggestions2">
                        @foreach(($tagSuggestions ?? []) as $suggestion)
                            <option value="{{ $suggestion }}"></option>
                        @endforeach
                    </datalist>
                    <div class="small text-muted mt-2 mb-1">Suggested tags:</div>
                    <div class="suggested-tags" id="suggestedTags2">
                        @foreach(array_slice(($tagSuggestions ?? []), 0, 8) as $suggestion)
                            <button type="button" class="suggested-tag-btn" data-tag="{{ $suggestion }}">{{ $suggestion }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="tags" id="hiddenTags2" value="{{ isset($tagFilters) ? implode(',', $tagFilters) : request('tags', '') }}">
                    <div id="selectedTags2" class="mt-2"></div>
                </div>
            </div>
        </form>

        @if($documents->count())
        <div id="search-results" class="row g-4 grid-view results-container">
            @foreach($documents as $doc)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-truncate" title="{{ $doc->title }}">
                            {{ $doc->title }}
                        </h5>
                        <div class="small mb-2"><span class="text-muted">Author:</span> {{ $doc->author }}</div>
                        <div class="small mb-2"><span class="text-muted">Advisor(s):</span> {{ $doc->advisors ?: '—' }}</div>
                        <div class="small mb-2"><span class="text-muted">Publication Date:</span> {{ optional($doc->publication_date)->format('F d, Y') ?: '—' }}</div>
                        <div class="small mb-2"><span class="text-muted">Tags:</span> {{ $doc->tags ?: '—' }}</div>
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
        <div class="results-container">
            <div class="no-results">
                <i class="bi bi-search fs-1 text-secondary mb-3"></i>
                <h4 class="mb-2">No results found</h4>
                <p class="text-muted mb-3">We couldn't find any documents matching your search criteria.</p>
                <a href="{{ route('mides.dashboard') }}" class="btn btn-pink">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
        @endif
    </div>

    <div style="height: 120px;"></div>
    @include('footer')

    <script>
        // JS for dynamic program dropdown
        (function() {
            function updateProgramDropdown(type) {
                var col = document.getElementById('mides-program-col-2');
                var progSel = document.getElementById('mides-program-select-2');
                var typeLower = (type || '').toString().toLowerCase();

                // Hide for Faculty/Theses/Dissertations or when no type selected
                if (!type || typeLower.indexOf('faculty') !== -1 || typeLower.indexOf('dissertation') !== -1) {
                    col.style.display = 'none';
                    if (progSel) progSel.value = '';
                    return;
                }

                // Show for other types and fetch programs
                col.style.display = 'block';
                fetch('/mides/programs?type=' + encodeURIComponent(type))
                    .then(function(resp) {
                        return resp.json();
                    })
                    .then(function(data) {
                        if (progSel) {
                            // Store current selection
                            var currentValue = progSel.value;
                            progSel.innerHTML = '<option value="">SELECT PROGRAM</option>';
                            if (Array.isArray(data.programs)) {
                                data.programs.forEach(function(prog) {
                                    var opt = document.createElement('option');
                                    // Always use name as value for backward compatibility
                                    opt.value = prog.name;
                                    opt.textContent = prog.name;
                                    // Restore selection if it matches
                                    if (currentValue && prog.name == currentValue) {
                                        opt.selected = true;
                                    }
                                    progSel.appendChild(opt);
                                });
                            }
                        }
                    });
            }

            var typeSelect = document.getElementById('mides-type-select-2');
            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    updateProgramDropdown(typeSelect.value);
                });
                // Initialize on page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        updateProgramDropdown(typeSelect.value);
                    });
                } else {
                    updateProgramDropdown(typeSelect.value);
                }
            }
        })();

        (function() {
            var toggleBtn = document.getElementById('toggleTagSearch2');
            var tagBox = document.getElementById('tagFilterBox2');
            var tagInput = document.getElementById('tagInput2');
            var selectedTagsEl = document.getElementById('selectedTags2');
            var suggestedTagsEl = document.getElementById('suggestedTags2');
            var hiddenTags = document.getElementById('hiddenTags2');
            var tagList = [];

            function renderTags() {
                selectedTagsEl.innerHTML = '';
                tagList.forEach(function(tag, idx) {
                    var chip = document.createElement('span');
                    chip.className = 'tag-chip';
                    chip.innerHTML = '<span>' + tag + '</span><button type="button" data-idx="' + idx + '">&times;</button>';
                    selectedTagsEl.appendChild(chip);
                });
                hiddenTags.value = tagList.join(',');
            }

            function addTag(value) {
                var tag = (value || '').trim().toLowerCase();
                if (!tag || tagList.indexOf(tag) !== -1) return;
                tagList.push(tag);
                renderTags();
            }

            var initial = (hiddenTags.value || '').split(',').map(function(v){ return v.trim().toLowerCase(); }).filter(Boolean);
            tagList = Array.from(new Set(initial));
            tagBox.style.display = 'block';
            renderTags();

            toggleBtn.addEventListener('click', function() {
                var isHidden = tagBox.style.display === 'none' || tagBox.style.display === '';
                tagBox.style.display = isHidden ? 'block' : 'none';
                toggleBtn.innerHTML = isHidden
                    ? '<i class="bi bi-tags"></i> Hide Tag Search'
                    : '<i class="bi bi-tags"></i> Show Tag Search';
            });

            tagInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTag(tagInput.value);
                    tagInput.value = '';
                }
            });

            selectedTagsEl.addEventListener('click', function(e) {
                if (e.target.tagName.toLowerCase() === 'button') {
                    var idx = parseInt(e.target.getAttribute('data-idx'), 10);
                    if (!isNaN(idx)) {
                        tagList.splice(idx, 1);
                        renderTags();
                    }
                }
            });

            if (suggestedTagsEl) {
                suggestedTagsEl.addEventListener('click', function(e) {
                    if (e.target.tagName.toLowerCase() === 'button') {
                        addTag(e.target.getAttribute('data-tag'));
                    }
                });
            }
        })();

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