@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title','Add Sidlak Journal')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ request('return', route('sidlak.manage')) }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Manage</a>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" form="sidlak-create-form" class="btn btn-success px-4 shadow-sm">Save</button>
                <a href="{{ request('return', route('sidlak.manage')) }}" class="btn btn-outline-secondary px-4 shadow-sm">Cancel</a>
            </div>
        </div>

        <h2 class="fw-bold mb-3 text-pink" style="letter-spacing: 1px; font-size: 2rem;">Add Sidlak Journal</h2>

        {{-- ✅ Display Success or Error Messages --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ✅ Display Validation Errors --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <h6 class="fw-bold"><i class="bi bi-exclamation-circle me-1"></i> Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="sidlak-create-form" method="POST" action="{{ route('sidlak.store') }}" enctype="multipart/form-data" class="card border-0 shadow-lg p-4 mb-4 bg-white rounded-4">
            @csrf
            <input type="hidden" name="return_url" value="{{ request('return', url()->previous()) }}">
            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Journal Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control form-control-lg bg-light border-0 shadow-sm @error('title') is-invalid @enderror" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Month & Year</label>
                    <input type="month" name="month_year" value="{{ old('month_year') }}" class="form-control form-control-lg bg-light border-0 shadow-sm @error('month_year') is-invalid @enderror">
                    @error('month_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Print ISSN</label>
                    <input type="text" name="print_issn" value="{{ old('print_issn') }}" class="form-control form-control-lg bg-light border-0 shadow-sm @error('print_issn') is-invalid @enderror" placeholder="e.g. 2350-8337">
                    @error('print_issn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Cover Photo</label>
                <input type="file" name="cover_photo" class="form-control bg-light border-0 shadow-sm @error('cover_photo') is-invalid @enderror" accept="image/*" onchange="previewCover(event)">
                @error('cover_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="mt-3 text-center">
                    <img id="cover-preview" src="#" alt="Cover Preview" class="img-fluid rounded-4 shadow" style="max-height: 250px; display: none; object-fit:cover;">
                </div>
            </div>
            <hr class="my-4">
            <h4 class="fw-bold mb-3 text-primary">Editors</h4>
            <div id="editors-list" class="row g-3"></div>
            <button type="button" class="btn btn-outline-primary mb-3 rounded-pill px-4 shadow-sm" onclick="addEditor()"><i class="bi bi-plus-circle me-1"></i>Add Editor</button>
            <hr class="my-4">
            <h4 class="fw-bold mb-3 text-primary">Peer Reviewers</h4>
            <div id="reviewers-list" class="row g-3"></div>
            <button type="button" class="btn btn-outline-primary mb-3 rounded-pill px-4 shadow-sm" onclick="addReviewer()"><i class="bi bi-plus-circle me-1"></i>Add Peer Reviewer</button>
            <hr>
            <h4 class="fw-bold mb-3">Research Articles</h4>
            <div id="articles-list"></div>
            <button type="button" class="btn btn-outline-primary mb-3" onclick="addArticle()">Add Article</button>
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ request('return', route('sidlak.manage')) }}" class="btn btn-outline-secondary px-4 shadow-sm">Cancel</a>
                <button type="submit" class="btn btn-success px-4 shadow-sm">Save Journal & Articles</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('management-scripts')
<div id="sidlak-old-data"
     data-editors='@json(old('editors', []))'
     data-reviewers='@json(old('peer_reviewers', []))'
     data-articles='@json(old('articles', []))'>
</div>
<script>
    (function() {
        var el = document.getElementById('sidlak-old-data');
        if (!el) return;
        try { window.oldEditors = JSON.parse(el.dataset.editors || '[]'); } catch (e) { window.oldEditors = []; }
        try { window.oldReviewers = JSON.parse(el.dataset.reviewers || '[]'); } catch (e) { window.oldReviewers = []; }
        try { window.oldArticles = JSON.parse(el.dataset.articles || '[]'); } catch (e) { window.oldArticles = []; }
    })();
    
</script>
<script src="{{ asset('js/sidlak-create.js') }}"></script>
@endpush
