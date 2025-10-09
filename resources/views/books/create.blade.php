@include('navbar')

<div class="container py-5">
    <style>
    /* Local responsive tweaks for book create form */
    @media (max-width:767px){
        .card-body .row .col-md-4, .card-body .row .col-md-6 { flex: 0 0 100%; max-width:100%; }
    }
    /* Typography and input border improvements for clarity */
    .books-typo { font-weight: 600; text-shadow: 0 0 0 rgba(0,0,0,0.02); }
    .form-control, .form-select, textarea.form-control {
        border-width: 1.6px !important;
        border-color: rgba(0,0,0,0.12) !important;
        box-shadow: none !important;
    }
    .form-control:focus, .form-select:focus, textarea.form-control:focus {
        border-color: #d81b60 !important;
        box-shadow: 0 4px 18px rgba(216,27,96,0.08) !important;
        outline: none !important;
    }
    </style>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-pink ">
                    <h4 class="mb-0">Add Book to Catalog</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('books.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label books-typo">Title</label>
                            <input name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label books-typo">Authors</label>
                            <input name="authors" class="form-control" value="{{ old('authors') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="row g-2">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label books-typo">Call Number</label>
                                    <input name="call_number" class="form-control" value="{{ old('call_number') }}">
                            </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label books-typo">Sublocation</label>
                                    <input name="sublocation" class="form-control" value="{{ old('sublocation') }}">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label books-typo">Published</label>
                                <input name="published" class="form-control" value="{{ old('published') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label books-typo">Copyright</label>
                                <input name="copyright" class="form-control" value="{{ old('copyright') }}">
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">Format</label>
                                <input name="format" class="form-control" value="{{ old('format') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">Content Type</label>
                                <input name="content_type" class="form-control" value="{{ old('content_type') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">Media Type</label>
                                <input name="media_type" class="form-control" value="{{ old('media_type') }}">
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">Carrier Type</label>
                                <input name="carrier_type" class="form-control" value="{{ old('carrier_type') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">ISSN</label>
                                <input name="issn" class="form-control" value="{{ old('issn') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">ISBN</label>
                                <input name="isbn" class="form-control" value="{{ old('isbn') }}">
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">LCCN</label>
                                <input name="lccn" class="form-control" value="{{ old('lccn') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">Barcode</label>
                                <input name="barcode" class="form-control" value="{{ old('barcode') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label books-typo">Status</label>
                                <input name="status" class="form-control" value="{{ old('status', 'Available') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Info</label>
                            <textarea name="additional_info" class="form-control" rows="3">{{ old('additional_info') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-pink" type="submit">Save Book</button>
                            <a href="{{ route('books.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer')
