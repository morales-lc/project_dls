<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $category }} Theses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('navbar')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">{{ $category }} Theses</h2>
            <div>
                <a href="{{ route('mides.graduate') }}" class="btn btn-outline-secondary me-2">&#8592; Back to Categories</a>
                <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-primary">MIDES Dashboard</a>
            </div>
        </div>
        <form class="row g-2 mb-3" method="GET" action="">
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
                <button type="submit" class="btn btn-primary w-100">Filter/Search</button>
            </div>
        </form>

        @if($documents->count())
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Title</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->author }}</td>
                    <td>{{ $doc->year }}</td>
                    <td>{{ $doc->title }}</td>
                    <td>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $doc->id }}">View</button>
                        @auth
                            @if(optional(auth()->user()->studentFaculty)->id)
                                <form method="POST" action="{{ route('bookmarks.toggle') }}" class="d-inline ms-2">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $doc->id }}">
                                    <input type="hidden" name="type" value="mides">
                                    <button type="submit" class="btn btn-outline-warning btn-sm">Bookmark</button>
                                </form>
                            @endif
                        @endauth
                        <!-- PDF Modals -->
                        @foreach($documents as $doc)
                        <div class="modal fade" id="pdfModal{{ $doc->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $doc->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pdfModalLabel{{ $doc->id }}">{{ $doc->title }} ({{ $doc->year }})</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <iframe
                                        src="{{ route('mides.viewer', $doc->id) }}"
                                        width="100%"
                                        height="100%"
                                        style="border:none; min-height:70vh;">
                                    </iframe>


                                </div>
                            </div>
                        </div>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $documents->links() }}
        </div>
        @else
        <div class="alert alert-info">No theses found for this category.</div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>