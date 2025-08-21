@include('navbar')
<div class="container py-4">
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

