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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td>{{ $record->title }}</td>
                        <td>{{ $record->author }}</td>
                        <td>{{ $record->year }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $record->pdf_path) }}" target="_blank" class="btn btn-primary btn-sm">View PDF</a>
                            <a href="{{ asset('storage/' . $record->pdf_path) }}" download="{{ basename($record->pdf_path) }}" class="btn btn-success btn-sm ms-2">Download PDF</a>
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
</div>

