@include('navbar')

<style>
/* Local styles for book index */
.book-grid .card { min-height:160px; }
.book-grid .card-title { font-size:1rem; font-weight:700; }
@media (max-width:767px) {
    .book-grid .card { min-height:140px; }
}
/* stronger typography and input border styles (index) */
.books-typo { font-weight:600; }
.book-grid .card { border-width: 1.2px; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Book Catalog</h4>
        <a href="{{ route('books.create') }}" class="btn btn-pink">Add Book</a>
    </div>

    <div class="row g-3 book-grid">
        @foreach($books as $book)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate">{{ $book->title }}</h5>
                        @if(!empty($book->authors))
                            <p class="text-muted small mb-2">{{ $book->authors }}</p>
                        @endif
                        @if(!empty($book->description))
                            <p class="flex-grow-1 small">{{ Str::limit($book->description, 140) }}</p>
                        @endif
                        <div class="mt-2 d-flex gap-2">
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($books->hasPages())
        <div class="mt-4 d-flex justify-content-center" aria-label="Book list pagination">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@include('footer')
