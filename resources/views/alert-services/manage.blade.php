@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title', 'Alert Services Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Alert Services Management</h2>

        </div>
        <div class="d-flex">

            <a href="{{ route('alert-services.create', ['return' => request()->fullUrl()]) }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.1rem;"><i class="bi bi-plus-lg"></i> Add New Book</a>
        </div>
        <div style="height: 30px;"></div>
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
                <a href="{{ route('alert-services.manage') }}" class="btn btn-pink w-100">Clear</a>
            </div>
        </form>

        <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                    <thead class="table-pink">
                        <tr style="font-size:1.05rem;">
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr class="book-row" style="cursor: pointer;"
                            data-id="{{ $book->id }}"
                            data-title="{{ $book->title ?? 'Untitled' }}"
                            data-author="{{ $book->author ?? '-' }}"
                            data-call-number="{{ $book->call_number ?? '-' }}"
                            data-department="{{ $book->department->name ?? '-' }}"
                            data-month="{{ DateTime::createFromFormat('!m', $book->month)->format('F') }}"
                            data-year="{{ $book->year }}"
                            data-cover-url="{{ $book->cover_image ? asset('storage/'.$book->cover_image) : '' }}"
                            data-pdf-url="{{ $book->pdf_path ? asset('storage/'.$book->pdf_path) : '' }}"
                            data-edit-url="{{ route('alert-services.edit', [$book->id, 'return' => request()->fullUrl()]) }}"
                            data-destroy-url="{{ route('alert-services.destroy', $book->id) }}">
                            <td class="fw-semibold">{{ $book->title ?? 'Untitled' }}</td>
                            <td class="text-muted">{{ $book->author ?? '-' }}</td>
                            <td>{{ DateTime::createFromFormat('!m', $book->month)->format('F') }} {{ $book->year }}</td>
                            <td>
                                <a href="{{ route('alert-services.edit', [$book->id, 'return' => request()->fullUrl()]) }}" class="btn btn-sm btn-warning px-3 me-1" data-no-row-click>Edit</a>
                                <form action="{{ route('alert-services.destroy', $book->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                    <button type="submit" class="btn btn-sm btn-danger px-3" onclick="return confirm('Delete this book?')" data-no-row-click>Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No books found.</td>
                        </tr>
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
<!-- Details Modal -->
<div class="modal fade" id="bookDetailsModal" tabindex="-1" aria-labelledby="bookDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header" style="background: linear-gradient(135deg, #f8bbd0, #f48fb1); color: #4a0033;">
                <h5 class="modal-title fw-bold" id="bookDetailsModalLabel">Book Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-4 d-flex flex-column align-items-center">
                        <div id="bookCoverWrap" class="shadow-sm rounded-3 overflow-hidden" style="width: 100%; max-width: 220px; aspect-ratio: 3/4; background:#f7f7f9; display:flex; align-items:center; justify-content:center;">
                            <img id="bookCoverImg" src="" alt="Cover" style="width:100%; height:100%; object-fit:cover; display:none;">
                            <div id="noCover" class="text-muted" style="font-size:0.95rem;">
                                <i class="bi bi-image" style="font-size:1.6rem;"></i>
                                <div>No cover image</div>
                            </div>
                        </div>
                        <div class="mt-3 w-100 d-grid gap-2">
                            <a id="openPdfBtn" href="#" target="_blank" class="btn btn-outline-primary" style="display:none;">
                                <i class="bi bi-file-earmark-pdf"></i> Open PDF
                            </a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <div class="d-flex align-items-start justify-content-between">
                                <h3 class="fw-bold mb-1" id="detailTitle" style="color:#880e4f;"></h3>
                                <span class="badge rounded-pill bg-secondary-subtle text-dark px-3 py-2" id="detailDept"></span>
                            </div>
                            <div class="text-muted">By <span id="detailAuthor">-</span></div>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="small text-uppercase text-muted">Call Number</div>
                                <div id="detailCallNumber" class="fw-semibold">-</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="small text-uppercase text-muted">Date</div>
                                <div id="detailDate" class="fw-semibold">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div class="text-muted small">
                    Tip: Click outside or press Esc to close
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a id="editBookBtn" href="#" class="btn btn-warning">Edit</a>
                    <form id="deleteBookForm" action="#" method="POST" onsubmit="return confirm('Delete this book?');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Subtle row hover to hint clickability */
    .table tbody tr.book-row:hover {
        background: #fff0f5;
        /* lavenderblush-like */
        transform: translateY(-1px);
        transition: all 120ms ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('bookDetailsModal');
        var modal = new bootstrap.Modal(modalEl);

        function setText(id, text) {
            var el = document.getElementById(id);
            if (el) el.textContent = text || '-';
        }

        document.querySelectorAll('tr.book-row').forEach(function(row) {
            row.addEventListener('click', function(e) {
                // Populate details
                var title = this.dataset.title || 'Untitled';
                var author = this.dataset.author || '-';
                var callNumber = this.dataset.callNumber || '-';
                var department = this.dataset.department || '-';
                var month = this.dataset.month || '-';
                var year = this.dataset.year || '-';
                var coverUrl = this.dataset.coverUrl || '';
                var pdfUrl = this.dataset.pdfUrl || '';
                var editUrl = this.dataset.editUrl || '#';
                var destroyUrl = this.dataset.destroyUrl || '#';

                setText('detailTitle', title);
                setText('detailAuthor', author);
                setText('detailCallNumber', callNumber);
                setText('detailDate', month + ' ' + year);

                var deptEl = document.getElementById('detailDept');
                if (deptEl) deptEl.textContent = department || '-';

                var coverImg = document.getElementById('bookCoverImg');
                var noCover = document.getElementById('noCover');
                if (coverUrl) {
                    coverImg.src = coverUrl;
                    coverImg.style.display = 'block';
                    noCover.style.display = 'none';
                } else {
                    coverImg.style.display = 'none';
                    noCover.style.display = 'block';
                }

                var pdfBtn = document.getElementById('openPdfBtn');
                if (pdfUrl) {
                    pdfBtn.href = pdfUrl;
                    pdfBtn.style.display = 'block';
                } else {
                    pdfBtn.style.display = 'none';
                }

                var editBtn = document.getElementById('editBookBtn');
                if (editBtn) editBtn.href = editUrl;

                var deleteForm = document.getElementById('deleteBookForm');
                if (deleteForm) deleteForm.action = destroyUrl;

                modal.show();
            });
        });
    });

    // Prevent row click when clicking on action buttons inside the row
    document.querySelectorAll('[data-no-row-click]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endsection