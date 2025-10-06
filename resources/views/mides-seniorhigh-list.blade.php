@include('navbar')
<div class="container py-4">
    <form class="row g-2 mb-3" method="GET" action="">
        <div class="col-md-3">
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
    <h2 class="mb-4 fw-bold">{{ $program }} Research Papers</h2>
    @if($records->isEmpty())
    <div class="alert alert-info">No records found for this program.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ $record->title }}</td>
                    <td>{{ $record->author }}</td>
                    <td>{{ $record->year }}</td>
                    <td>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $record->id }}">View</button>
                        @auth
                            @if(optional(auth()->user()->studentFaculty)->id)
                                <form method="POST" action="{{ route('bookmarks.toggle') }}" class="d-inline ms-2">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $record->id }}">
                                    <input type="hidden" name="type" value="mides">
                                    <button type="submit" class="btn btn-outline-warning btn-sm">Bookmark</button>
                                </form>
                            @endif
                        @endauth
                        <!-- PDF Modal -->
                        <div class="modal fade" id="pdfModal{{ $record->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $record->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pdfModalLabel{{ $record->id }}">{{ $record->title }} ({{ $record->year }})</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <iframe
                                        src="{{ route('mides.seniorhigh.viewer', $record->id) }}"
                                        width="100%"
                                        height="100%"
                                        style="border:none; min-height:70vh;">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    <div class="row mt-5">
        <div class="col-md-12 text-center">
            <a href="{{ route('mides.seniorhigh.programs') }}" class="btn btn-outline-dark">Back to Programs</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>