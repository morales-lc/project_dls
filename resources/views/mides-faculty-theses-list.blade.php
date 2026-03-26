<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Faculty Publications, Theses & Dissertations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/mides.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mides-scholar.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        .filter-shell {
            border: 1px solid #f5cada;
            border-radius: 1rem;
            padding: .9rem;
            background: linear-gradient(140deg, #fff 0%, #fff5fa 100%);
            box-shadow: 0 10px 26px rgba(232, 62, 140, 0.12);
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

        .card.h-100 {
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
            border-radius: 0.85rem;
            overflow: hidden;
            border: 2px solid #f0d3e0;
            background-color: #fff;
        }

        .card.h-100:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 10px 30px rgba(216, 27, 96, 0.14), 0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: #d81b60;
        }

        .results-container {
            min-height: 320px;
        }

        .list-view .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .list-view .card {
            flex-direction: row;
            align-items: stretch;
        }

        .list-view .card-body {
            flex: 1;
        }

        .list-view .card-footer {
            min-width: 220px;
            flex-direction: column;
            justify-content: center;
            gap: .45rem;
        }

        .tag-discovery-card {
            margin-top: .4rem;
            padding: .75rem;
            border-radius: .9rem;
            border: 2px solid #f28bb8;
            background: linear-gradient(145deg, #fff7fc 0%, #fff 100%);
            box-shadow: 0 6px 20px rgba(232, 62, 140, 0.12);
        }

        .tag-toggle-btn {
            border: none;
            color: #fff;
            background: #e83e8c;
            font-weight: 700;
            border-radius: 999px;
            padding: .35rem .85rem;
        }

        .tag-filter-box {
            display: block;
            margin-top: .65rem;
            padding: .65rem;
            border-radius: .75rem;
            border: 1px solid #f4bfd5;
            background: #fff;
        }

        .suggested-tags {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
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
            padding: .3rem .6rem;
            border-radius: 999px;
            background: #ffd9e9;
            color: #7a0f42;
            font-size: .82rem;
            font-weight: 600;
            margin: .18rem;
        }

        .tag-chip button {
            border: none;
            background: transparent;
            color: inherit;
            line-height: 1;
        }
    </style>

</head>

<body class="mides-scholar">
    @include('navbar')

    <div class="container py-5 mides-wrap">
        <div class="card shadow-lg w-100 border-0" style="max-width:1100px; margin:auto;">
            <div class="card-header bg-pink d-flex align-items-center">
                <i class="bi bi-collection fs-3 me-2"></i>
                <span class="fw-bold fs-5">Faculty Publications, Theses & Dissertations</span>
                <div class="ms-auto">
                    <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-pink">MIDES Dashboard</a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-2 mb-md-0">Faculty Documents</h4>
                    <div class="view-toggle mides-view-toggle">
                        <button type="button" class="btn btn-outline-secondary btn-sm active" id="grid-view-btn-faculty"><i class="bi bi-grid-3x3-gap-fill"></i> Grid</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="list-view-btn-faculty"><i class="bi bi-list-ul"></i> List</button>
                    </div>
                </div>

                <form class="row g-2 mb-4 mides-search-form" method="GET" action="" id="facultyFilterForm">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by title, author, advisor, date, tags..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-select">
                            <option value="publication_date" {{ request('sort') == 'publication_date' ? 'selected' : '' }}>Publication Date</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                            <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Author</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="direction" class="form-select">
                            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-pink w-100">Filter/Search</button>
                    </div>

                    <div class="col-12">
                        <div class="mides-tag-box">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold" style="color:#a31358;"><i class="bi bi-stars"></i> Tag Discovery</div>
                                    <div class="small text-muted">Find similar faculty work through smart tag combinations.</div>
                                </div>
                                <button type="button" class="btn tag-toggle-btn" id="toggleTagSearchFaculty"><i class="bi bi-tags"></i> Hide Tag Search</button>
                            </div>
                            <div id="tagFilterBoxFaculty" class="tag-filter-box">
                                <input type="text" id="tagInputFaculty" class="form-control" list="facultyTagSuggestions" placeholder="Type a tag and press Enter">
                                <datalist id="facultyTagSuggestions">
                                    @foreach(($tagSuggestions ?? []) as $suggestion)
                                        <option value="{{ $suggestion }}"></option>
                                    @endforeach
                                </datalist>
                                <div class="small text-muted mt-2 mb-1">Suggested tags:</div>
                                <div class="suggested-tags" id="suggestedTagsFaculty">
                                    @foreach(array_slice(($tagSuggestions ?? []), 0, 8) as $suggestion)
                                        <button type="button" class="suggested-tag-btn" data-tag="{{ $suggestion }}">{{ $suggestion }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="tags" id="hiddenTagsFaculty" value="{{ isset($tagFilters) ? implode(',', $tagFilters) : request('tags', '') }}">
                                <div id="selectedTagsFaculty" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </form>

                @if($documents->count())
                <div id="faculty-results" class="row g-2 results-container mides-results">
                    @foreach($documents as $doc)
                    <div class="col-12">
                        <div class="paper-item">
                            <div class="card-body">
                                <h5 class="paper-title" title="{{ $doc->title }}">{{ $doc->title }}</h5>
                                <div class="paper-meta">{{ $doc->author }} | {{ optional($doc->publication_date)->format('F d, Y') ?: '—' }} | {{ $doc->type ?? 'Faculty/Theses/Dissertations' }}</div>
                                <div class="paper-meta">Advisor(s): {{ $doc->advisors ?: '—' }}</div>
                                <div class="paper-abstract">{{ \Illuminate\Support\Str::limit($doc->description ?: 'No abstract provided.', 220) }}</div>
                                <div class="mt-2">
                                    @if($doc->tags)
                                        @foreach(array_slice(array_filter(array_map('trim', explode(',', $doc->tags))), 0, 4) as $tag)
                                            <span class="paper-pill">{{ $tag }}</span>
                                        @endforeach
                                    @endif
                                </div>
                                <a href="{{ route('mides.document.show', $doc->id) }}" class="stretched-link" aria-label="View details for {{ $doc->title }}"></a>
                            </div>
                            <div class="paper-footer d-flex justify-content-end align-items-center px-3 py-2">
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
                                    <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} d-flex align-items-center gap-1 bookmark-btn" style="border-radius: 8px;">
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
                <div class="mt-4 d-flex justify-content-center">{{ $documents->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
                @else
                <div class="alert alert-info">No faculty publications, theses, or dissertations found.</div>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('mides.dashboard') }}" class="btn btn-secondary">Back to MIDES Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var gridBtn = document.getElementById('grid-view-btn-faculty');
            var listBtn = document.getElementById('list-view-btn-faculty');
            var container = document.getElementById('faculty-results');
            if (!gridBtn || !listBtn || !container) return;

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

        (function() {
            var toggleBtn = document.getElementById('toggleTagSearchFaculty');
            var tagBox = document.getElementById('tagFilterBoxFaculty');
            var tagInput = document.getElementById('tagInputFaculty');
            var selectedTagsEl = document.getElementById('selectedTagsFaculty');
            var suggestedTagsEl = document.getElementById('suggestedTagsFaculty');
            var hiddenTags = document.getElementById('hiddenTagsFaculty');
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

        // Lazy-load iframe src for PDF modals to avoid calling viewer route on page load
        document.querySelectorAll('.modal').forEach(modalEl => {
            modalEl.addEventListener('show.bs.modal', function(e) {
                const iframe = this.querySelector('iframe[data-src]');
                if (iframe && (!iframe.src || iframe.src === 'about:blank')) {
                    iframe.src = iframe.getAttribute('data-src');
                }
            });
            modalEl.addEventListener('hidden.bs.modal', function(e) {
                const iframe = this.querySelector('iframe[data-src]');
                if (iframe) {
                    iframe.src = 'about:blank';
                }
            });
        });
    </script>

    <div style="height: 100px;"></div>
    @include('footer')
</body>

</html>
<script>
    // Generic bookmark toggle handler (AJAX)
    (function(){
        async function handleToggle(e){
            e.preventDefault();
            const form = e.target.closest('.bookmark-toggle') || e.target;
            if (!form) return;
            const btn = form.querySelector('.bookmark-btn');
            const icon = btn.querySelector('i');
            const textEl = btn.querySelector('.bookmark-text');
            const formData = new FormData(form);
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

            try{
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value
                    },
                    body: formData
                });

                if (!resp.ok) throw new Error('Network response not ok');
                const data = await resp.json();
                if (data.status === 'bookmarked'){
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-pink');
                    if (icon) icon.className = 'bi bi-bookmark-fill';
                    if (textEl) textEl.textContent = 'Bookmarked';
                } else {
                    btn.classList.remove('btn-pink');
                    btn.classList.add('btn-outline-warning');
                    if (icon) icon.className = 'bi bi-bookmark';
                    if (textEl) textEl.textContent = 'Bookmark';
                }
            }catch(err){
                console.error('Bookmark toggle failed', err);
            }finally{
                btn.disabled = false;
                if (btn.innerHTML.includes('spinner-border')){
                    btn.innerHTML = originalHtml;
                }
            }
        }

        document.addEventListener('submit', function(e){
            if (e.target && e.target.classList && e.target.classList.contains('bookmark-toggle')){
                handleToggle(e);
            }
        });
    })();
</script>