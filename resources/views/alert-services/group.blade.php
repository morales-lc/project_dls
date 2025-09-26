<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Services - {{ $displayName }}</title>
    <style>
        .netflix-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem 1.5rem;
            margin-bottom: 2rem;
            justify-items: center;
        }
        .netflix-cover {
            width: 90%;
            max-width: 280px;
            height: 380px;
            object-fit: cover;
            background: #f7f8fa;
            box-shadow: 0 8px 32px 0 rgba(40,40,60,0.18), 0 1.5px 8px 0 rgba(216,27,96,0.10);
            border-radius: 1.2rem;
            transition: transform .18s, box-shadow .18s;
            cursor: pointer;
        }
        .netflix-cover:hover {
            transform: scale(1.06);
            box-shadow: 0 16px 48px 0 rgba(216,27,96,0.22), 0 2px 12px 0 rgba(40,40,60,0.10);
        }
        .netflix-cover:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 24px 0 rgba(216,27,96,0.18);
        }
        @media (max-width: 900px) {
            .netflix-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .netflix-grid {
                grid-template-columns: 1fr;
                gap: 1.2rem 0.5rem;
            }
            .netflix-cover {
                height: 220px;
            }
        }
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
            @foreach($books as $book)
                <div class="d-flex flex-column align-items-center" style="width:100%;">
                    @if($book->cover_image)
                        <a href="{{ asset('storage/'.$book->pdf_path) }}" target="_blank">
                            <img src="{{ asset('storage/'.$book->cover_image) }}" class="netflix-cover" alt="Cover">
                        </a>
                    @else
                        <div class="d-flex align-items-center justify-content-center netflix-cover bg-light text-muted" style="height:300px;">No Cover</div>
                    @endif
                    <div class="fw-semibold text-center mt-2" style="font-size:1.08rem; color:#d81b60; max-width:90%; word-break:break-word;">{{ $book->title ?? 'Untitled' }}</div>
                    <a href="{{ route('lira.jotform', ['examplePurposive' => $book->title]) }}" class="btn btn-sm btn-pink mt-2" style="background:#e83e8c;color:#fff;border-radius:1em;font-weight:600;">
                        <i class="bi bi-journal-bookmark-fill"></i> Request via LiRA
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning mt-4">No books found for this group.</div>
    @endif
</div>
</body>
</html>
