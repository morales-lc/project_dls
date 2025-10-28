@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    #articles-list {
        background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
    }

    .article-row,
    .editor-row,
    .reviewer-row {
        transition: opacity 0.5s;
        opacity: 1;
        border: 0.5px solid rgba(149, 149, 149, 1);
        border-radius: 1rem;
        background: #fff;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.07);
    }

    .article-row.fade-out {
        opacity: 0.2;
    }

    .editor-row,
    .reviewer-row,
    .article-row {
        padding: 1rem 0.5rem 0.5rem 0.5rem;
    }
    /* Make research article cards a bit roomier */
    .article-row {
        padding: 1rem !important;
        margin-bottom: 1.25rem !important;
    }
</style>
@endpush

@section('title', 'Edit Sidlak Journal')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ request('return', route('sidlak.manage')) }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Manage</a>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" form="sidlak-edit-form" class="btn btn-success px-4 shadow-sm">Update</button>
                <a href="{{ request('return', route('sidlak.manage')) }}" class="btn btn-outline-secondary px-4 shadow-sm">Cancel</a>
            </div>
        </div>

        <h2 class="fw-bold mb-3 text-pink" style="letter-spacing: 1px; font-size: 2rem;">Edit Sidlak Journal</h2>

        <form id="sidlak-edit-form" method="POST" action="{{ route('sidlak.update', $journal->id) }}" enctype="multipart/form-data" class="card border-0 shadow-lg p-4 mb-4 bg-white rounded-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="return_url" value="{{ request('return', url()->previous()) }}">

            <div class="d-flex justify-content-end mb-3 d-md-none">
                <button type="submit" class="btn btn-success px-4 shadow-sm">Update</button>
                <a href="{{ request('return', route('sidlak.manage')) }}" class="btn btn-outline-secondary px-4 shadow-sm ms-2">Cancel</a>
            </div>

            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Journal Title</label>
                    <input type="text" name="title" class="form-control form-control-lg bg-light border-0 shadow-sm" value="{{ $journal->title }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Month & Year</label>
                    <input type="month" name="month_year" class="form-control form-control-lg bg-light border-0 shadow-sm" value="{{ $journal->year }}-{{ str_pad(date('m', strtotime($journal->month)), 2, '0', STR_PAD_LEFT) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Print ISSN</label>
                    <input type="text" name="print_issn" class="form-control form-control-lg bg-light border-0 shadow-sm" value="{{ $journal->print_issn }}" pattern="\d{4}-\d{4}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Cover Photo</label>
                <input type="file" name="cover_photo" class="form-control bg-light border-0 shadow-sm" accept="image/*">
                @if($journal->cover_photo)
                <div class="mt-3 text-center">
                    <img src="{{ asset('storage/' . $journal->cover_photo) }}" alt="Current Cover" class="img-fluid rounded-4 shadow" style="max-height:250px;object-fit:cover;">
                </div>
                @endif
            </div>

            <hr class="my-4">
            <h4 class="fw-bold mb-3 text-primary">Editors</h4>
            <div id="editors-list" class="row g-3">
                @foreach($journal->editors as $idx => $editor)
                <div class="row g-3 mb-2 editor-row">
                    <div class="col-md-8">
                        <label class="form-label">Editor Name</label>
                        <input type="text" name="editors[{{ $idx }}][name]" class="form-control" value="{{ $editor->name }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="editors[{{ $idx }}][title]" class="form-control" value="{{ $editor->title }}" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger w-100" onclick="confirmRemoveEditor(this)">Remove</button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary mb-3 rounded-pill px-4 shadow-sm" onclick="addEditor()"><i class="bi bi-plus-circle me-1"></i>Add Editor</button>

            <hr class="my-4">
            <h4 class="fw-bold mb-3 text-primary">Peer Reviewers</h4>
            <div id="reviewers-list" class="row g-3">
                @foreach($journal->peerReviewers as $idx => $reviewer)
                <div class="row g-2 mb-2 reviewer-row">
                    <div class="col-md-5">
                        <label class="form-label">Reviewer Name</label>
                        <input type="text" name="peer_reviewers[{{ $idx }}][name]" class="form-control" value="{{ $reviewer->name }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Title</label>
                        <input type="text" name="peer_reviewers[{{ $idx }}][title]" class="form-control" value="{{ $reviewer->title }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger w-100" onclick="confirmRemoveReviewer(this)">Remove</button>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <label class="form-label">Institution</label>
                        <input type="text" name="peer_reviewers[{{ $idx }}][institution]" class="form-control" value="{{ $reviewer->institution }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="peer_reviewers[{{ $idx }}][city]" class="form-control" value="{{ $reviewer->city }}" required>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary mb-3 rounded-pill px-4 shadow-sm" onclick="addReviewer()"><i class="bi bi-plus-circle me-1"></i>Add Peer Reviewer</button>

            <hr class="my-4">
            <h4 class="fw-bold mb-3">Research Articles</h4>
            <div id="articles-list" class="p-3 rounded-4 bg-light shadow-sm border border-2 border-primary mb-4">
                @foreach($journal->articles as $idx => $article)
                <div class="row g-3 mb-3 p-3 article-row">
                    <input type="hidden" name="articles[{{ $idx }}][id]" value="{{ $article->id }}">
                    <div class="col-md-5">
                        <label class="form-label">Article Title</label>
                        <input type="text" name="articles[{{ $idx }}][title]" class="form-control border-primary shadow-sm" value="{{ $article->title }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Authors</label>
                        <input type="text" name="articles[{{ $idx }}][authors]" class="form-control border-primary shadow-sm" value="{{ $article->authors }}" required>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label">PDF File</label>
                        <input type="file" name="articles[{{ $idx }}][pdf_file]" class="form-control border-primary shadow-sm" accept="application/pdf">
                        @if($article->pdf_file)
                        <a href="{{ asset('storage/' . $article->pdf_file) }}" target="_blank" class="d-block mt-2">Current PDF</a>
                        @endif
                    </div>
                    <div class="col-md-12 mt-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-danger remove-article-btn px-3" data-idx="{{ $idx }}"><i class="bi bi-trash me-1"></i>Remove Article</button>
                        <input type="hidden" name="articles[{{ $idx }}][remove]" value="0" class="remove-flag">
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary mb-3" onclick="addArticle()">Add Article</button>
        </form>
    </div>
</div>
@endsection

@push('management-scripts')
<script>
    let articleIdx = Number("{{ $journal->articles->count() }}");

    function addArticle() {
        const list = document.getElementById('articles-list');
        const row = document.createElement('div');
        row.className = 'row g-3 mb-3 p-3 article-row';
        row.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Article Title</label>
            <input type="text" name="articles[${articleIdx}][title]" class="form-control border-primary shadow-sm" required>
        </div>
        <div class="col-md-5">
            <label class="form-label">Authors</label>
            <input type="text" name="articles[${articleIdx}][authors]" class="form-control border-primary shadow-sm" required>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">PDF File</label>
            <input type="file" name="articles[${articleIdx}][pdf_file]" class="form-control border-primary shadow-sm" accept="application/pdf" required>
        </div>
        <div class="col-md-12 mt-3 d-flex justify-content-end">
            <button type="button" class="btn btn-sm btn-danger remove-article-btn px-3"><i class="bi bi-trash me-1"></i>Remove Article</button>
        </div>
    `;
        list.appendChild(row);
        articleIdx++;
    }

    // Remove existing article using event delegation
    document.addEventListener('DOMContentLoaded', function() {
        function handleRemoveArticle(btn) {
            if (confirm('Are you sure you want to remove this article?')) {
                let row = btn.parentElement;
                while (row && !(row.classList.contains('article-row'))) {
                    row = row.parentElement;
                }
                if (row) {
                    row.classList.add('fade-out');
                    setTimeout(function() {
                        row.style.display = 'none';
                        const flag = row.querySelector('.remove-flag');
                        if (flag) flag.value = '1';
                    }, 500);
                }
            }
        }
        document.querySelectorAll('.remove-article-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                handleRemoveArticle(btn);
            });
        });
        // For dynamically added articles
        const articlesList = document.getElementById('articles-list');
        if (articlesList) {
            articlesList.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-article-btn')) {
                    handleRemoveArticle(e.target);
                }
            });
        }
    });
</script>

<script>
    // Only declare these variables once at the top of the script block
    let editorIdx = parseInt('{{ $journal->editors->count() }}');
    let reviewerIdx = parseInt('{{ $journal->peerReviewers->count() }}');

    function addEditor() {
        const list = document.getElementById('editors-list');
        const row = document.createElement('div');
        row.className = 'row g-3 mb-2';
        row.innerHTML = `
        <div class="col-md-8">
            <label class="form-label">Editor Name</label>
            <input type="text" name="editors[${editorIdx}][name]" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Title</label>
            <input type="text" name="editors[${editorIdx}][title]" class="form-control" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-danger w-100" onclick="confirmRemoveEditor(this)">Remove</button>
        </div>
    `;
        list.appendChild(row);
        editorIdx++;
    }

    function addReviewer() {
        const list = document.getElementById('reviewers-list');
        const row = document.createElement('div');
        row.className = 'row g-3 mb-2';
        row.innerHTML = `
        <div class="col-md-3">
            <label class="form-label">Reviewer Name</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][name]" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Title</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][title]" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Institution</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][institution]" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">City</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][city]" class="form-control" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-danger w-100" onclick="confirmRemoveReviewer(this)">Remove</button>
        </div>
    `;
        list.appendChild(row);
        reviewerIdx++;
    }

    function confirmRemoveEditor(btn) {
        if (confirm('Are you sure you want to remove this editor?')) {
            btn.closest('.row').remove();
        }
    }

    function confirmRemoveReviewer(btn) {
        if (confirm('Are you sure you want to remove this peer reviewer?')) {
            btn.closest('.row').remove();
        }
    }
</script>

@endpush