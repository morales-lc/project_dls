@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'MARC Import')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Import Catalog</h2>
            <small class="text-muted">Upload MARC (.mrc/.marc) files to import records</small>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card p-4 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="card-body">
                <form id="marcForm" action="{{ route('marc.import') }}" method="POST" enctype="multipart/form-data"
                      data-upload-url="{{ route('marc.import') }}" data-csrf="{{ csrf_token() }}">
                    @csrf
                    <div class="mb-3">
                        <label for="marc_file" class="form-label fw-semibold">Upload MARC file</label>
                        <input type="file" name="marc_file" id="marc_file" accept=".mrc,.marc" class="form-control" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" id="importBtn" class="btn btn-pink px-4 py-2 fw-semibold">
                            <i class="bi bi-upload"></i> Import
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                    </div>

                    <div id="uploadStatus" class="mt-3" style="display:none;">
                        <div class="progress mb-2" style="height:14px;">
                            <div id="uploadProgress" class="progress-bar progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width:0%"></div>
                        </div>
                        <div id="uploadMessage" class="small text-muted"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('management-scripts')
<script>
    (function() {
        const form = document.getElementById('marcForm');
        const uploadUrl = form.dataset.uploadUrl;
        const csrfToken = form.dataset.csrf;
        const fileInput = document.getElementById('marc_file');
        const importBtn = document.getElementById('importBtn');
        const statusWrap = document.getElementById('uploadStatus');
        const progressBar = document.getElementById('uploadProgress');
        const message = document.getElementById('uploadMessage');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!fileInput.files || !fileInput.files.length) {
                alert('Please select a MARC file to upload.');
                return;
            }

            importBtn.disabled = true;
            statusWrap.style.display = 'block';
            progressBar.style.width = '0%';
            message.textContent = 'Preparing upload...';

            const fd = new FormData();
            fd.append('_token', csrfToken);
            fd.append('marc_file', fileInput.files[0]);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl, true);
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.addEventListener('progress', function(ev) {
                if (ev.lengthComputable) {
                    const pct = Math.round((ev.loaded / ev.total) * 100);
                    progressBar.style.width = pct + '%';
                    progressBar.textContent = pct + '%';
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    importBtn.disabled = false;
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.success) {
                                progressBar.style.width = '100%';
                                message.innerHTML = '<span class="text-success">' + (res.message || 'Import succeeded.') + '</span>';
                                if (res.output) {
                                    message.innerHTML += '<div class="mt-2 small text-muted">' + escapeHtml(res.output) + '</div>';
                                }
                                setTimeout(() => {
                                    statusWrap.style.display = 'none';
                                    progressBar.style.width = '0%';
                                    message.textContent = '';
                                }, 5000);
                            } else {
                                message.innerHTML = '<span class="text-danger">' + (res.message || 'Import failed.') + '</span>';
                            }
                        } catch (err) {
                            message.innerHTML = '<span class="text-success">Import finished. Reloading.</span>';
                            setTimeout(() => window.location.reload(), 800);
                        }
                    } else {
                        let txt = 'Import failed. Server returned status ' + xhr.status;
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.message) txt += ': ' + json.message;
                        } catch (e) {}
                        message.innerHTML = '<span class="text-danger">' + escapeHtml(txt) + '</span>';
                    }
                }
            };

            xhr.onerror = function() {
                importBtn.disabled = false;
                message.innerHTML = '<span class="text-danger">Network error during upload.</span>';
            };

            xhr.send(fd);
        });

        function escapeHtml(str) {
            return String(str).replace(/[&<>"'`]/g, function(s) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": "&#39;",
                    "`": "&#96;"
                })[s];
            });
        }
    })();
</script>
@endpush
