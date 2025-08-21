@include('navbar')
<div class="container py-4">
    <h2 class="fw-bold mb-3">Faculty/Theses/Dissertations</h2>
    @if($documents->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
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

