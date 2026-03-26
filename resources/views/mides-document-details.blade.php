<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc->title }} - MIDES</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/mides-scholar.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #f7f8fb 0%, #ffffff 70%);
            color: #1f2937;
        }

        .paper-shell {
            max-width: 980px;
            margin: 0 auto;
        }

        .paper-card {
            border: 1px solid #e9edf3;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            background: #fff;
        }

        .paper-title {
            font-size: 1.9rem;
            line-height: 1.2;
            color: #0f172a;
        }

        .meta-line {
            color: #4b5563;
            font-size: .95rem;
        }

        .meta-line strong {
            color: #111827;
        }

        .abstract-box {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fcfcfd;
        }

        .action-btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .badge-topic {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .related-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            transition: all .18s ease;
        }

        .related-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            border-color: #d1d5db;
        }
    </style>
</head>

<body class="mides-scholar">
    @include('navbar')

    <div class="container py-4 py-md-5">
        <div class="paper-shell">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-dark btn-sm">MIDES Dashboard</a>
            </div>

            <div class="paper-card p-4 p-md-5 mb-4">
                <div class="mb-3">
                    <span class="badge badge-topic">{{ $doc->type }}</span>
                    @if($doc->midesCategory)
                        <span class="badge bg-light text-dark border">{{ $doc->midesCategory->name }}</span>
                    @elseif($doc->category)
                        <span class="badge bg-light text-dark border">{{ $doc->category }}</span>
                    @elseif($doc->program)
                        <span class="badge bg-light text-dark border">{{ $doc->program }}</span>
                    @endif
                </div>

                <h1 class="paper-title fw-bold mb-3">{{ $doc->title }}</h1>

                <div class="meta-line mb-2"><strong>Author:</strong> {{ $doc->author }}</div>
                <div class="meta-line mb-2"><strong>Advisor(s):</strong> {{ $doc->advisors ?: '—' }}</div>
                <div class="meta-line mb-2"><strong>Publication Date:</strong> {{ optional($doc->publication_date)->format('F d, Y') ?: '—' }}</div>
                <div class="meta-line mb-4">
                    <strong>Tags:</strong>
                    @php
                        $docTags = collect(explode(',', (string) $doc->tags))
                            ->map(fn($tag) => trim($tag))
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp
                    @if($docTags->isNotEmpty())
                        @foreach($docTags as $tag)
                            <a href="{{ route('mides.tag', ['tag' => rawurlencode($tag)]) }}" class="badge bg-light text-dark border text-decoration-none">#{{ $tag }}</a>
                        @endforeach
                    @else
                        <span>—</span>
                    @endif
                </div>

                <div class="abstract-box p-3 p-md-4 mb-4">
                    <div class="fw-bold mb-2" style="color:#111827;">Abstract / Description</div>
                    <div style="white-space: pre-line; color:#374151;">
                        {{ $doc->description ?: 'No abstract/description provided for this document yet.' }}
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('mides.search.viewer', $doc->id) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary action-btn">
                        <i class="bi bi-eye me-1"></i> View Document
                    </a>
                    <a href="{{ asset('storage/' . $doc->pdf_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary action-btn">
                        <i class="bi bi-download me-1"></i> Download PDF
                    </a>
                    @if(Auth::check() && Auth::user()->role !== 'guest' && $sf && $sf->id)
                        <form method="POST" action="{{ route('bookmarks.toggle') }}" class="bookmark-toggle m-0">
                            @csrf
                            <input type="hidden" name="id" value="{{ $doc->id }}">
                            <input type="hidden" name="type" value="mides">
                            <button type="submit" class="btn {{ $isBookmarked ? 'btn-success' : 'btn-outline-warning' }} action-btn bookmark-btn">
                                <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }} me-1"></i>
                                <span class="bookmark-text">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($relatedDocuments->isNotEmpty())
                <div class="mb-2 fw-bold">Related Documents Across MIDES Collections</div>
                <div class="row g-3">
                    @foreach($relatedDocuments as $related)
                        <div class="col-md-6">
                            <a href="{{ route('mides.document.show', $related->id) }}" class="text-decoration-none text-dark">
                                <div class="related-item p-3 h-100">
                                    <div class="fw-semibold mb-1">{{ $related->title }}</div>
                                    <div class="small text-muted mb-1">{{ $related->author }}</div>
                                    <div class="small mb-1">
                                        <span class="badge bg-light text-dark border">{{ $related->type }}</span>
                                        @if($related->midesCategory)
                                            <span class="badge bg-white text-secondary border">{{ $related->midesCategory->name }}</span>
                                        @elseif($related->category)
                                            <span class="badge bg-white text-secondary border">{{ $related->category }}</span>
                                        @elseif($related->program)
                                            <span class="badge bg-white text-secondary border">{{ $related->program }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted mb-1">
                                        {{ \Illuminate\Support\Str::limit($related->description ?: 'No abstract/description available.', 140) }}
                                    </div>
                                    <div class="small text-muted">{{ optional($related->publication_date)->format('F d, Y') ?: '—' }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div style="height: 80px;"></div>
    @include('footer')

    <script>
        (function(){
            async function handleToggle(e){
                e.preventDefault();
                const form = e.target.closest('.bookmark-toggle') || e.target;
                if (!form) return;
                const btn = form.querySelector('.bookmark-btn');
                const textEl = form.querySelector('.bookmark-text');
                const icon = btn.querySelector('i');
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
