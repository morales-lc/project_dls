let articleIdx = 0;
let editorIdx = 0;
let reviewerIdx = 0;

function addEditor(name = '', title = '') {
    const list = document.getElementById('editors-list');
    const row = document.createElement('div');
    row.className = 'row g-3 mb-2';
    row.innerHTML = `
        <div class="col-md-8">
            <label class="form-label">Editor Name</label>
            <input type="text" name="editors[${editorIdx}][name]" class="form-control" value="${escapeHtml(name)}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Title</label>
            <input type="text" name="editors[${editorIdx}][title]" class="form-control" value="${escapeHtml(title)}" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-danger w-100" onclick="confirmRemoveEditor(this)">Remove</button>
        </div>
    `;
    list.appendChild(row);
    editorIdx++;
}

function addReviewer(name = '', title = '', institution = '', city = '') {
    const list = document.getElementById('reviewers-list');
    const row = document.createElement('div');
    row.className = 'row g-3 mb-2';
    row.innerHTML = `
        <div class="col-md-8">
            <label class="form-label">Reviewer Name</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][name]" class="form-control" value="${escapeHtml(name)}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Title</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][title]" class="form-control" value="${escapeHtml(title)}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Institution</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][institution]" class="form-control" value="${escapeHtml(institution)}" required>
        </div>
        <div class="col-md-5">
            <label class="form-label">City</label>
            <input type="text" name="peer_reviewers[${reviewerIdx}][city]" class="form-control" value="${escapeHtml(city)}" required>
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

function addArticle(title = '', authors = '') {
    const list = document.getElementById('articles-list');
    const div = document.createElement('div');
    div.className = 'card p-3 mb-3';
    div.innerHTML = `
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label">Article Title</label>
                <input type="text" name="articles[${articleIdx}][title]" class="form-control" value="${escapeHtml(title)}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Authors</label>
                <input type="text" name="articles[${articleIdx}][authors]" class="form-control" value="${escapeHtml(authors)}" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">PDF File</label>
                <input type="file" name="articles[${articleIdx}][pdf_file]" class="form-control" accept="application/pdf" ${title ? '' : 'required'}>
                ${title ? '<small class="text-muted">Leave blank to keep existing file (if re-uploading)</small>' : ''}
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">Remove Article</button>
    `;
    list.appendChild(div);
    articleIdx++;
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
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

document.addEventListener('DOMContentLoaded', function() {
    const oldEditors = window.oldEditors || [];
    oldEditors.forEach((editor) => {
        addEditor(editor.name || '', editor.title || '');
    });

    const oldReviewers = window.oldReviewers || [];
    oldReviewers.forEach((reviewer) => {
        addReviewer(
            reviewer.name || '', 
            reviewer.title || '', 
            reviewer.institution || '', 
            reviewer.city || ''
        );
    });

    const oldArticles = window.oldArticles || [];
    oldArticles.forEach((article) => {
        addArticle(article.title || '', article.authors || '');
    });
});
