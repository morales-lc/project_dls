
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Services Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ 'css/styles.css' }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light" style="min-height: 100vh;">
    <div id="dashboardWrapper" class="d-flex position-relative">
        @include('components.admin-sidebar')
        <div class="flex-grow-1">
           
            <div class="container py-5 d-flex flex-column align-items-center justify-content-center">
        <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
                <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Alert Services Control Panel</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('alert-services.create') }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;"><i class="bi bi-plus-lg"></i> Add New Book</a>
                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Filter & Sort Form -->
            <form method="GET" action="{{ route('alert-services.manage') }}" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by title, year..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="year" {{ request('sort') == 'year' ? 'selected' : '' }}>Year</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="direction" class="form-select">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('alert-services.manage') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>

            <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
                <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                    <thead class="table-pink">
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
                                <a href="{{ route('alert-services.edit', $book->id) }}" class="btn btn-sm btn-warning px-3 me-1">Edit</a>
                                <form action="{{ route('alert-services.destroy', $book->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-3" onclick="return confirm('Delete this book?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No books found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
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


