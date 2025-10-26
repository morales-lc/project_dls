<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Services - {{ $displayName }}</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
        }

        /* --- GRID --- */
        .netflix-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            /* 5 columns on desktop */
            gap: 2rem 1.2rem;
            justify-items: center;
            margin-bottom: 2rem;
        }

        /* Divider inside the grid should span full width of the grid row */
        .netflix-grid .divider {
            grid-column: 1 / -1;
            /* span all columns */
            height: 4px;
            background: #e83e8c;
            border-radius: 2px;
            margin: 1rem 0;
            /* vertical spacing */
            width: 100%;
        }

        /* On very small screens reduce divider spacing */
        @media (max-width: 600px) {
            .netflix-grid .divider {
                margin: 0.6rem 0;
            }
        }

        /* --- RESPONSIVE BREAKPOINTS --- */
        @media (max-width: 1200px) {
            .netflix-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .netflix-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .netflix-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ✅ 3 columns per row on small mobile view */
        @media (max-width: 600px) {
            .netflix-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem 0.5rem;
            }

            .book-card {
                max-width: 100%;
                transform: scale(0.9);
            }

            .book-title {
                font-size: 0.8rem;
            }

            .btn-pink {
                font-size: 0.75rem;
                padding: 0.3em 0.8em;
            }
        }

        /* ultra small phones (≤400px) → 2 columns */
        @media (max-width: 400px) {
            .netflix-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* --- ROW DIVIDER --- */
        .divider {
            width: 100%;
            height: 4px;
            background: #e83e8c;
            /* pink */
            border-radius: 2px;
            margin: 2rem 0;
        }

        /* --- CARD --- */
        .book-card {
            position: relative;
            width: 100%;
            max-width: 280px;
            border-radius: 1.2rem;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(40, 40, 60, 0.18),
                0 1.5px 8px rgba(216, 27, 96, 0.10);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
        }

        .book-card:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 36px rgba(216, 27, 96, 0.25),
                0 2px 12px rgba(40, 40, 60, 0.1);
        }

        /* --- COVER IMAGE --- */
        .book-card img {
            width: 100%;
            aspect-ratio: 3 / 4.3;
            object-fit: cover;
            display: block;
            transition: filter 0.25s ease;
        }

        /* --- OVERLAY --- */
        .book-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.05));
            color: #fff;
            opacity: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding: 1.2rem;
            transition: opacity 0.3s ease;
        }

        .book-card:hover .book-overlay {
            opacity: 1;
        }

        /* --- TITLE --- */
        .book-title {
            font-weight: 600;
            text-align: center;
            font-size: 1rem;
            margin-bottom: 0.8rem;
            line-height: 1.2em;
            display: -webkit-box;
            line-clamp: 2;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* --- BUTTON --- */
        .btn-pink {
            background: #e83e8c;
            color: #fff;
            border-radius: 1.2em;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.45em 1.2em;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .btn-pink:hover {
            background: #d61b72;
            color: #fff;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 900px) {
            .netflix-grid {
                gap: 1.5rem 1rem;
            }
        }

        @media (max-width: 600px) {
            .book-card {
                max-width: 100%;
            }

            .book-title {
                font-size: 0.95rem;
            }

            .btn-pink {
                font-size: 0.85rem;
                width: 100%;
                text-align: center;
            }
        }

        /* Bookmark button variations */
        .btn-bookmark {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 1.2em;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.45em 1.2em;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-bookmark:hover { background: rgba(255, 255, 255, 0.2); }
        .btn-bookmarked { background: #28a745; border-color: #28a745; color: #fff; }
    </style>
</head>

<body>
    @include('navbar')

    <div class="container py-4">
        <a href="{{ route('alert-services.index') }}" class="btn btn-outline-secondary mb-3">&larr; Back to Alert Services</a>
        <h2 class="fw-bold mb-2">{{ $displayName }}</h2>
        <h5 class="mb-4 text-muted">{{ $displayMonth }} {{ $year }}</h5>

        @if($books->count())
        <div class="netflix-grid">
            @php $count = 0; @endphp
            @foreach($books as $book)
            <div class="book-card">
                @if($book->cover_image)
                <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Book Cover">
                @else
                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="aspect-ratio:3/4.3;">No Cover</div>
                @endif

                <div class="book-overlay">
                    <div class="book-title">{{ $book->title ?? 'Untitled' }}</div>
                    @if(Auth::check() && Auth::user()->role !== 'guest')
                    <div class="d-flex flex-column gap-2 w-100">
                        <form action="{{ route('bookmarks.toggle') }}" method="POST" class="bookmark-toggle-alert d-flex justify-content-center">
                            @csrf
                            <input type="hidden" name="id" value="{{ $book->id }}">
                            <input type="hidden" name="type" value="alert_book">
                            @php $isBookmarked = isset($bookmarkedIds) && in_array($book->id, $bookmarkedIds); @endphp
                            <button type="submit" class="btn-bookmark {{ $isBookmarked ? 'btn-bookmarked' : '' }}">
                                <i class="bi {{ $isBookmarked ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus' }} me-1"></i>
                                <span class="label">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                            </button>
                        </form>
                        <a href="{{ route('lira.jotform', ['title' => $book->title, 'author' => $book->author, 'call_number' => $book->call_number]) }}"
                            target="_blank" rel="noopener noreferrer" class="btn-pink text-center">
                            <i class="bi bi-journal-bookmark-fill"></i> Request via LiRA
                        </a>
                    </div>
                    @elseif(!Auth::check())
                    <a href="{{ route('login') }}" onclick="alert('Please log in to bookmark and request via LiRA.');" class="btn-bookmark text-center">
                        <i class="bi bi-box-arrow-in-right"></i> Log in to bookmark
                    </a>
                    <a href="{{ route('login') }}" onclick="alert('Please log in to request via LiRA.');" class="btn-pink">
                        <i class="bi bi-journal-bookmark-fill"></i> Request via LiRA
                    </a>
                    @endif
                </div>
            </div>

            @php $count++; @endphp

            {{-- Add divider every full row (5 cards desktop, 4/3/2 mobile responsive) --}}
            @if($count % 5 == 0)
            <div class="divider"></div>
            @endif
            @endforeach
        </div>
        @else
        <div class="alert alert-warning mt-4">No books found for this group.</div>
        @endif


    </div>

    @include('footer')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.bookmark-toggle-alert').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        var btn = form.querySelector('button');
                        if (!btn) return;
                        var label = btn.querySelector('.label');
                        var icon = btn.querySelector('i');
                        var originalHTML = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing';

                        var formData = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                            },
                            body: formData
                        }).then(function(res) {
                            // handle 403 separately to show message
                            if (!res.ok && res.status === 403) return res.json().then(function(data){ throw new Error(data.message || 'Forbidden'); });
                            return res.json();
                        }).then(function(data) {
                            if (!data || !data.status) return;
                            if (data.status === 'bookmarked') {
                                btn.classList.add('btn-bookmarked');
                                if (icon) { icon.className = 'bi bi-bookmark-check-fill me-1'; }
                                if (label) { label.textContent = 'Bookmarked'; }
                            } else if (data.status === 'removed') {
                                btn.classList.remove('btn-bookmarked');
                                if (icon) { icon.className = 'bi bi-bookmark-plus me-1'; }
                                if (label) { label.textContent = 'Bookmark'; }
                            }
                        }).catch(function(err) {
                            console.error(err);
                            alert(err && err.message ? err.message : 'Failed to update bookmark.');
                        }).finally(function() {
                            btn.disabled = false;
                            // Restore button contents to reflect updated state (icon/label already set)
                            if (label) {
                                btn.innerHTML = '';
                                var i = document.createElement('i');
                                i.className = icon ? icon.className : 'bi bi-bookmark-plus me-1';
                                var span = document.createElement('span');
                                span.className = 'label';
                                span.textContent = label ? label.textContent : 'Bookmark';
                                btn.appendChild(i);
                                btn.appendChild(span);
                            } else {
                                btn.innerHTML = originalHTML;
                            }
                        });
                    });
                });
            });
        </script>
</body>

</html>