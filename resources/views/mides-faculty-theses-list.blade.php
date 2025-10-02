@include('navbar')

<div class="container py-4">
    <h2 class="fw-bold mb-3">Faculty/Theses/Dissertations</h2>
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
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mides-table-striped">
<link href="{{ asset('css/mides.css') }}" rel="stylesheet">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        <tr>
                            <td>{{ $doc->title }}</td>
                            <td>{{ $doc->author }}</td>
                            <td>{{ $doc->year }}</td>
                            <td>
                                <a href="{{ asset('storage/' . $doc->pdf_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">View PDF</a>
                                <a href="{{ asset('storage/' . $doc->pdf_path) }}" download="{{ basename($doc->pdf_path) }}" class="btn btn-outline-success btn-sm">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    @else
        <div class="alert alert-info">No faculty publications, theses, or dissertations found.</div>
    @endif
    <div class="mt-4 text-center">
        <a href="{{ route('mides.dashboard') }}" class="btn btn-secondary">Back to MIDES Dashboard</a>
    </div>
</div>

