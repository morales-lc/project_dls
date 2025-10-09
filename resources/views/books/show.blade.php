@include('navbar')

<style>
/* Styles just for book views */
.book-detail-wrap { padding: 2.25rem 0; }
.book-cover { width:100%; height:320px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:6px; overflow:hidden; }
.book-cover img { max-width:100%; max-height:100%; object-fit:contain; }
.book-meta .label { font-size: .9rem; color: #6c757d; }
.book-meta .value { font-weight:600; color:#222; }
@media (max-width: 767px) {
    .book-cover { height:200px; }
    .book-detail-wrap { padding: 1.25rem 0; }
}
</style>

<div class="container book-detail-wrap">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="bg-white rounded shadow-sm p-3" style="border:1px solid #ffdfe9;">
                        <div class="book-cover">
                            <img src="{{ asset('images/book-placeholder.png') }}" alt="Cover">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8">
                    <h3 class="fw-bold mb-2">{{ $book->title }}</h3>
                    @if(!empty($book->authors))
                        <div class="text-muted mb-2">{{ $book->authors }}</div>
                    @endif

                    @if(!empty($book->description))
                        <div class="mb-3">{{ $book->description }}</div>
                    @endif

                    <div class="row row-cols-1 row-cols-md-2 g-2 book-meta">
                        @if(!empty($book->call_number))
                        <div class="col">
                            <div class="label">Call Number</div>
                            <div class="value">{{ $book->call_number }}</div>
                        </div>
                        @endif

                        @if(!empty($book->sublocation))
                        <div class="col">
                            <div class="label">Sublocation</div>
                            <div class="value">{{ $book->sublocation }}</div>
                        </div>
                        @endif

                        @if(!empty($book->published))
                        <div class="col">
                            <div class="label">Published</div>
                            <div class="value">{{ $book->published }}</div>
                        </div>
                        @endif

                        @if(!empty($book->copyright))
                        <div class="col">
                            <div class="label">Copyright</div>
                            <div class="value">{{ $book->copyright }}</div>
                        </div>
                        @endif

                        @if(!empty($book->isbn))
                        <div class="col">
                            <div class="label">ISBN</div>
                            <div class="value">{{ $book->isbn }}</div>
                        </div>
                        @endif

                        @if(!empty($book->issn))
                        <div class="col">
                            <div class="label">ISSN</div>
                            <div class="value">{{ $book->issn }}</div>
                        </div>
                        @endif

                        @if(!empty($book->lccn))
                        <div class="col">
                            <div class="label">LCCN</div>
                            <div class="value">{{ $book->lccn }}</div>
                        </div>
                        @endif

                        @if(!empty($book->barcode))
                        <div class="col">
                            <div class="label">Barcode</div>
                            <div class="value">{{ $book->barcode }}</div>
                        </div>
                        @endif

                        @if(!empty($book->status))
                        <div class="col">
                            <div class="label">Status</div>
                            <div class="value">{{ $book->status }}</div>
                        </div>
                        @endif
                    </div>

                    @if(!empty($book->additional_info))
                        <div class="mt-4">
                            <h6 class="fw-semibold">Additional Info</h6>
                            <div class="small">{{ $book->additional_info }}</div>
                        </div>
                    @endif

                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-primary" href="#">Add to list</a>
                        <a class="btn btn-pink" href="#">Request</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer')
