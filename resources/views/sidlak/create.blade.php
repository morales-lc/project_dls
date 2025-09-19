<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Sidlak Journal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);">
@include('navbar')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0 text-primary" style="letter-spacing:1px;">Add Sidlak Journal</h2>
        <div class="d-none d-md-flex align-items-center gap-2">
            <button type="submit" form="sidlak-create-form" class="btn btn-success px-4 shadow-sm">Save</button>
        </div>
    </div>
    <form id="sidlak-create-form" method="POST" action="{{ route('sidlak.store') }}" enctype="multipart/form-data" class="card border-0 shadow-lg p-4 mb-5 bg-white rounded-4">
        @csrf
        <div class="row g-4 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Journal Title</label>
                <input type="text" name="title" class="form-control form-control-lg bg-light border-0 shadow-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Month & Year</label>
                <input type="month" name="month_year" class="form-control form-control-lg bg-light border-0 shadow-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Print ISSN</label>
                <input type="text" name="print_issn" class="form-control form-control-lg bg-light border-0 shadow-sm" pattern="\d{4}-\d{4}" placeholder="e.g. 2350-8337" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Cover Photo</label>
            <input type="file" name="cover_photo" class="form-control bg-light border-0 shadow-sm" accept="image/*" onchange="previewCover(event)">
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
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-4 shadow-sm">Save Journal & Articles</button>
        </div>
    </form>
</div>
<script>
    let articleIdx = 0;
    let editorIdx = 0;
    let reviewerIdx = 0;

    function addEditor() {
        const list = document.getElementById('editors-list');
        const divCol = document.createElement('div');
        divCol.className = 'col-md-6';
        divCol.innerHTML = `
            <div class="card border-0 shadow-sm p-3 mb-3 bg-light rounded-4">
                <div class="row mb-2">
                    <div class="col-md-8">
                        <label class="form-label">Editor Name</label>
                        <input type="text" name="editors[${editorIdx}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Title</label>
                        <input type="text" name="editors[${editorIdx}][title]" class="form-control" required>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmRemoveEditor(this)">Remove Editor</button>
            </div>
        `;
        list.appendChild(divCol);
        editorIdx++;
    }

    function addReviewer() {
        const list = document.getElementById('reviewers-list');
        const divCol = document.createElement('div');
        divCol.className = 'col-md-6';
        divCol.innerHTML = `
            <div class="card border-0 shadow-sm p-3 mb-3 bg-light rounded-4">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Reviewer Name</label>
                        <input type="text" name="peer_reviewers[${reviewerIdx}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Title</label>
                        <input type="text" name="peer_reviewers[${reviewerIdx}][title]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Institution</label>
                        <input type="text" name="peer_reviewers[${reviewerIdx}][institution]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="peer_reviewers[${reviewerIdx}][city]" class="form-control" required>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmRemoveReviewer(this)">Remove Reviewer</button>
            </div>
        `;
        list.appendChild(divCol);
        reviewerIdx++;
    }

    function confirmRemoveEditor(btn) {
        if (confirm('Are you sure you want to remove this editor?')) {
            btn.parentElement.parentElement.remove();
        }
    }

    function confirmRemoveReviewer(btn) {
        if (confirm('Are you sure you want to remove this peer reviewer?')) {
            btn.parentElement.parentElement.remove();
        }
    }

    function addArticle() {
        const list = document.getElementById('articles-list');
        const div = document.createElement('div');
        div.className = 'card p-3 mb-3';
        div.innerHTML = `
            <div class="row mb-2">
                <div class="col-md-5">
                    <label class="form-label">Article Title</label>
                    <input type="text" name="articles[${articleIdx}][title]" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Authors</label>
                    <input type="text" name="articles[${articleIdx}][authors]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">PDF File</label>
                    <input type="file" name="articles[${articleIdx}][pdf_file]" class="form-control" accept="application/pdf" required>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">Remove Article</button>
        `;
        list.appendChild(div);
        articleIdx++;
    }

    function previewCover(event) {
        const input = event.target;
        const preview = document.getElementById('cover-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
