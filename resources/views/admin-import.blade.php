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
            <div>
                <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Import Catalog</h2>
                <small class="text-muted">Upload MARC (.mrc/.marc) files to import records</small>
                @php
                    $catalogLastUpdated = null;
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('catalogs')) {
                            if (\Illuminate\Support\Facades\Schema::hasColumn('catalogs', 'updated_at')) {
                                $catalogLastUpdated = \App\Models\Catalog::max('updated_at');
                            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('catalogs', 'created_at')) {
                                $catalogLastUpdated = \App\Models\Catalog::max('created_at');
                            }
                        }
                    } catch (\Throwable $e) {
                        $catalogLastUpdated = null;
                    }
                @endphp
                @if($catalogLastUpdated)
                    <div class="small text-muted mt-1">Last catalog update: {{ \Carbon\Carbon::parse($catalogLastUpdated)->format('M d, Y h:i A') }}</div>
                @else
                    <div class="small text-muted mt-1">No catalog updates yet</div>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('marc.import.logs') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-clock-history"></i> Import History
                </a>
                <small class="text-muted">{{ now()->format('M d, Y h:i A') }}</small>
            </div>
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
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="delete_missing" name="delete_missing">
                        <label class="form-check-label" for="delete_missing">Delete records missing from this file</label>
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
    const deleteMissing = document.getElementById('delete_missing');
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
            if (deleteMissing && deleteMissing.checked) {
                fd.append('delete_missing', '1');
            }

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
                                let html = '<span class="text-success fw-semibold"><i class="bi bi-check-circle me-2"></i>' + (res.message || 'Import succeeded.') + '</span>';
                                if (res.summary) {
                                    const s = res.summary;
                                    html += `<div class="mt-3 p-3 bg-light rounded">`+
                                        `<div class="row g-2 text-center">`+
                                        `<div class="col"><div class="small text-muted">Added</div><div class="h5 mb-0 text-success">${s.inserted}</div></div>`+
                                        `<div class="col"><div class="small text-muted">Updated</div><div class="h5 mb-0 text-info">${s.updated}</div></div>`+
                                        `<div class="col"><div class="small text-muted">Unchanged</div><div class="h5 mb-0 text-secondary">${s.unchanged}</div></div>`;
                                    if (s.deletion_mode === 'applied') {
                                        html += `<div class="col"><div class="small text-muted">Deleted</div><div class="h5 mb-0 text-danger">${s.deleted}</div></div>`;
                                    }
                                    if (s.errors && s.errors > 0) {
                                        html += `<div class="col"><div class="small text-muted">Errors</div><div class="h5 mb-0 text-warning">${s.errors}</div></div>`;
                                    }
                                    html += `</div></div>`;
                                    html += `<div class="mt-3 d-flex gap-2 justify-content-center">`+
                                        `<a href="{{ route('marc.import.logs') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-clock-history me-1"></i> View Import History</a>`;
                                    if (s.log_file) {
                                        const logUrl = `{{ url('admin/marc-logs') }}/${encodeURIComponent(s.log_file)}`;
                                        html += `<a href="${logUrl}" class="btn btn-sm btn-outline-secondary" download><i class="bi bi-download me-1"></i> Download Detailed Log</a>`;
                                    }
                                    html += `</div>`;
                                }
                                message.innerHTML = html;
                                // Keep the results and download button visible - don't hide automatically
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
