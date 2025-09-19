
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Services Management</title>
</head>
<body style="background: #f7f8fa; min-height: 100vh;">
    @include('navbar')
    <div class="container py-5 d-flex flex-column align-items-center justify-content-center">
        <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
                <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Alert Services Control Panel</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('alert-services.create') }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;">+ Add New Book</a>
                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive rounded-3 border bg-light-subtle p-2">
                <table class="table table-hover align-middle mb-0" style="background: #fff; border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                        <tr style="font-size:1.05rem;">
                            <th style="width:70px">Cover</th>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>PDF</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td>
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Cover" style="width:50px;height:70px;object-fit:cover; border-radius:0.5rem; box-shadow:0 2px 8px 0 rgba(40,40,60,0.10);">
                                @else
                                    <span class="text-muted">No Cover</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $book->title ?? 'Untitled' }}</td>
                            <td><span class="badge bg-secondary-subtle text-dark px-3 py-2" style="font-size:0.98rem;">{{ $book->department->name ?? '-' }}</span></td>
                            <td>{{ DateTime::createFromFormat('!m', $book->month)->format('F') }}</td>
                            <td>{{ $book->year }}</td>
                            <td><a href="{{ asset('storage/'.$book->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3">View PDF</a></td>
                            <td>
                                <a href="{{ route('alert-services.edit', $book->id) }}" class="btn btn-sm btn-outline-secondary px-3 me-1">Edit</a>
                                <form action="{{ route('alert-services.destroy', $book->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Delete this book?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No books found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        {{ $books->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


