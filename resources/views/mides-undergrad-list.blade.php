<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $program }} Theses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mides.css') }}">

    
</head>
<body>
@include('navbar')

<div class="container py-4">
    <div class="card shadow-lg w-100 border-0" style="max-width:1100px; margin:auto;">
        <div class="card-header bg-pink  d-flex align-items-center">
            <i class="bi bi-journal-bookmark fs-3 me-2"></i>
            <span class="fw-bold fs-5 flex-grow-1">{{ $program }} Theses</span>
            <div class="ms-auto d-flex flex-wrap gap-2">
                <a href="{{ route('mides.undergrad') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
                <a href="{{ route('mides.dashboard') }}" class="btn btn-light btn-sm text-pink fw-semibold"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </div>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <form class="row g-2 mb-4" method="GET" action="">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by title, author, year..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="year" {{ request('sort') == 'year' ? 'selected' : '' }}>Year</option>
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
            </form>

            <!-- Results Table -->
            @if($documents->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Author</th>
                                <th>Year</th>
                                <th>Title</th>
                                <th class="text-center">PDF</th>
                                <th class="text-center">Bookmark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                            <tr>
                                    <td>{{ $doc->author }}</td>
                                    <td>{{ $doc->year }}</td>
                                    <td>{{ $doc->title }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-pink btn-sm view-btn" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $doc->id }}">
                                            <i class="bi bi-file-earmark-pdf"></i> View
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="pdfModal{{ $doc->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $doc->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="pdfModalLabel{{ $doc->id }}">{{ $doc->title }} ({{ $doc->year }})</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <iframe src="{{ route('mides.undergrad.viewer', $doc->id) }}" width="100%" height="100%" style="border:none; min-height:70vh;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @auth
                                            @php
                                                $sf = optional(auth()->user()->studentFaculty);
                                                $isBookmarked = false;
                                                if ($sf && $sf->id) {
                                                    $isBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                                                        ->where('bookmarkable_type', \App\Models\MidesDocument::class)
                                                        ->where('bookmarkable_id', $doc->id)
                                                        ->exists();
                                                }
                                            @endphp
                                            @if($sf && $sf->id)
                                                <form method="POST" action="{{ route('bookmarks.toggle') }}" class="d-inline bookmark-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $doc->id }}">
                                                    <input type="hidden" name="type" value="mides">
                                                    <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} bookmark-btn">
                                                        <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                                                        {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $documents->links() }}</div>
            @else
                <div class="alert alert-info">No theses found for this program.</div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
