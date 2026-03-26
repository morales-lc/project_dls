<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>#{{ $tag }} - MIDES Tag Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/mides.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mides-scholar.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>

<body class="mides-scholar">
@include('navbar')

<div class="container py-5 mides-wrap">
    <div class="card shadow-lg w-100 border-0" style="max-width:1100px; margin:auto;">
        <div class="card-header bg-pink d-flex align-items-center" style="border-radius:1.25rem 1.25rem 0 0;">
            <i class="bi bi-hash fs-3 me-2"></i>
            <span class="fw-bold fs-5">MIDES Tag: {{ $tag }}</span>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-pink">MIDES Dashboard</a>
            </div>
        </div>

        <div class="card-body">
            <form class="row g-2 mb-4 mides-search-form" method="GET" action="{{ route('mides.tag', ['tag' => rawurlencode($tag)]) }}">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search within #{{ $tag }} documents...">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="publication_date" {{ ($sort ?? 'publication_date') === 'publication_date' ? 'selected' : '' }}>Sort: Date</option>
                        <option value="year" {{ ($sort ?? '') === 'year' ? 'selected' : '' }}>Sort: Year</option>
                        <option value="title" {{ ($sort ?? '') === 'title' ? 'selected' : '' }}>Sort: Title</option>
                        <option value="author" {{ ($sort ?? '') === 'author' ? 'selected' : '' }}>Sort: Author</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="direction" class="form-select">
                        <option value="desc" {{ ($direction ?? 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ ($direction ?? '') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            @if($documents->count())
                <div class="row g-2 mides-results">
                    @foreach($documents as $doc)
                        <div class="col-12">
                            <div class="paper-item p-3 h-100">
                                <a href="{{ route('mides.document.show', $doc->id) }}" class="stretched-link" aria-label="View details"></a>
                                <h5 class="paper-title mb-2">{{ $doc->title }}</h5>
                                <div class="paper-meta"><strong>Author:</strong> {{ $doc->author }}</div>
                                <div class="paper-meta"><strong>Type:</strong> {{ $doc->type }}</div>
                                <div class="paper-meta"><strong>Publication:</strong> {{ optional($doc->publication_date)->format('F d, Y') ?: '—' }}</div>
                                <div class="paper-abstract">
                                    {{ \Illuminate\Support\Str::limit($doc->description ?: 'No abstract/description provided.', 220) }}
                                </div>
                                <div class="paper-footer mt-3 pt-2 d-flex flex-wrap align-items-center gap-2">
                                    <span class="paper-pill">#{{ $tag }}</span>
                                    @if($doc->midesCategory)
                                        <span class="paper-pill">{{ $doc->midesCategory->name }}</span>
                                    @elseif($doc->category)
                                        <span class="paper-pill">{{ $doc->category }}</span>
                                    @elseif($doc->program)
                                        <span class="paper-pill">{{ $doc->program }}</span>
                                    @endif

                                    <div class="ms-auto d-flex gap-2" style="position:relative; z-index:2;">
                                        <a href="{{ route('mides.search.viewer', $doc->id) }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener noreferrer">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ asset('storage/' . $doc->pdf_path) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener noreferrer">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        @if(Auth::check() && Auth::user()->role !== 'guest' && $sf && $sf->id)
                                            @php
                                                $isBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                                                    ->where('bookmarkable_type', \App\Models\MidesDocument::class)
                                                    ->where('bookmarkable_id', $doc->id)
                                                    ->exists();
                                            @endphp
                                            <form method="POST" action="{{ route('bookmarks.toggle') }}" class="bookmark-toggle m-0">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $doc->id }}">
                                                <input type="hidden" name="type" value="mides">
                                                <button type="submit" class="btn {{ $isBookmarked ? 'btn-success' : 'btn-outline-warning' }} btn-sm bookmark-btn">
                                                    <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }} me-1"></i>
                                                    <span class="bookmark-text">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $documents->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="alert alert-info mb-0">No MIDES documents found for this tag.</div>
            @endif
        </div>
    </div>
</div>

<div style="height: 100px;"></div>
@include('footer')

<script>
    (function(){
        async function handleToggle(e){
            e.preventDefault();
            const form = e.target.closest('.bookmark-toggle') || e.target;
            if (!form) return;
            const btn = form.querySelector('.bookmark-btn');
            const icon = btn.querySelector('i');
            const textEl = btn.querySelector('.bookmark-text');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value
                    },
                    body: new FormData(form)
                });

                if (!resp.ok) throw new Error('Request failed');
                const data = await resp.json();
                if (data.status === 'bookmarked') {
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-success');
                    if (icon) icon.className = 'bi bi-bookmark-fill me-1';
                    if (textEl) textEl.textContent = 'Bookmarked';
                } else {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-warning');
                    if (icon) icon.className = 'bi bi-bookmark me-1';
                    if (textEl) textEl.textContent = 'Bookmark';
                }
            } catch (err) {
                console.error('Bookmark toggle failed', err);
            } finally {
                btn.disabled = false;
                if (btn.innerHTML.includes('spinner-border')) {
                    btn.innerHTML = originalHtml;
                }
            }
        }

        document.addEventListener('submit', function(e){
            if (e.target && e.target.classList && e.target.classList.contains('bookmark-toggle')) {
                handleToggle(e);
            }
        });
    })();
</script>
</body>

</html>
