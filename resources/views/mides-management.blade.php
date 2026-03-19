@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush


@section('title','MIDES Repository Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0 text-pink" style="letter-spacing: 1px; font-size: 2rem;">MIDES Repository Management</h2>

        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    <a href="{{ route('mides.upload', ['return' => request()->fullUrl()]) }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.05rem;"><i class="bi bi-plus-lg"></i> Add New Document</a>
    <a href="{{ route('mides.categories.panel', ['return' => request()->fullUrl()]) }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.05rem;"><i class="bi bi-plus-lg"></i> Manage MIDES Categories</a>
        <div style="height: 30px;"></div>
        <form method="GET" action="{{ route('mides.management') }}" class="row g-2 mb-3 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, advisor, date, tags..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($types as $t)
                    <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="publication_date" {{ $sort == 'publication_date' ? 'selected' : '' }}>Publication Date</option>
                    <option value="year" {{ $sort == 'year' ? 'selected' : '' }}>Year</option>
                    <option value="author" {{ $sort == 'author' ? 'selected' : '' }}>Author</option>
                    <option value="title" {{ $sort == 'title' ? 'selected' : '' }}>Title</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('mides.management') }}" class="btn btn-pink">Clear</a>
            </div>
        </form>

        <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-4">
                    <thead class="table-pink">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Advisor(s)</th>
                            <th>Publication Date</th>
                            <th>Tags</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $doc)
                        <tr class="doc-row" style="cursor:pointer;"
                            data-id="{{ $doc->id }}"
                            data-title="{{ $doc->title }}"
                            data-author="{{ $doc->author }}"
                            data-advisors="{{ $doc->advisors }}"
                            data-publication-date="{{ optional($doc->publication_date)->format('Y-m-d') }}"
                            data-tags="{{ $doc->tags }}"
                            data-type="{{ $typeNames[$doc->type] ?? $doc->type }}"
                            data-category="{{ optional($doc->midesCategory)->name ?? $doc->category ?? $doc->program ?? '—' }}"
                            data-pdf-url="{{ asset('storage/' . $doc->pdf_path) }}"
                            data-update-modal-id="updateModal{{ $doc->id }}"
                            data-delete-url="{{ route('mides.delete', $doc->id) }}">
                            <td>{{ $doc->title }}</td>
                            <td>{{ $doc->author }}</td>
                            <td>{{ $doc->advisors ?: '—' }}</td>
                            <td>{{ optional($doc->publication_date)->format('F d, Y') ?: ($doc->year ? $doc->year . '-01-01' : '—') }}</td>
                            <td>{{ $doc->tags ?: '—' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-no-row-click data-bs-toggle="modal" data-bs-target="#updateModal{{ $doc->id }}">Update</button>
                                <form method="POST" action="{{ route('mides.delete', $doc->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                    <button type="submit" class="btn btn-sm btn-danger" data-no-row-click>Delete</button>
                                </form>
                            </td>
                        </tr>
                        <!-- Update Modal -->
                        <div class="modal fade" id="updateModal{{ $doc->id }}" tabindex="-1" aria-labelledby="updateModalLabel{{ $doc->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('mides.update', $doc->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel{{ $doc->id }}">Update Document</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($errors->any())
                                                <div class="alert alert-danger">
                                                    <strong>Please fix the following errors:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <div class="mb-2">
                                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                                <select name="type" class="form-select" required>
                                                    <option value="">-- Select Type --</option>
                                                    @foreach($types as $t)
                                                    <option value="{{ $t }}" {{ ($doc->type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Category/Program <span class="text-danger">*</span></label>
                                                <select name="mides_category_id" class="form-select" id="midesCategorySelect{{ $doc->id }}" data-selected="{{ $doc->mides_category_id ?? '' }}" required>
                                                    <option value="">Select...</option>
                                                    @foreach(\App\Models\MidesCategory::where('type', $doc->type)->get() as $cat)
                                                    <option value="{{ $cat->id }}" {{ ($doc->mides_category_id == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                                {{-- Hidden legacy fields for compatibility (server still expects category/program in some places) --}}
                                                <input type="hidden" name="category" value="{{ $doc->category }}">
                                                <input type="hidden" name="program" value="{{ $doc->program }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Author <span class="text-danger">*</span></label>
                                                <input type="text" name="author" class="form-control" value="{{ old('doc_id') == $doc->id ? old('author') : $doc->author }}" maxlength="255" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Advisor(s)</label>
                                                <input type="text" name="advisors" class="form-control" value="{{ old('doc_id') == $doc->id ? old('advisors') : $doc->advisors }}" maxlength="1000">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Publication Date <span class="text-danger">*</span></label>
                                                @php
                                                    $modalDate = old('doc_id') == $doc->id ? old('publication_date') : optional($doc->publication_date)->format('Y-m-d');
                                                    $modalYear = $modalDate ? \Illuminate\Support\Carbon::parse($modalDate)->format('Y') : '';
                                                    $modalMonth = $modalDate ? \Illuminate\Support\Carbon::parse($modalDate)->format('m') : '';
                                                    $modalDay = $modalDate ? \Illuminate\Support\Carbon::parse($modalDate)->format('d') : '';
                                                @endphp
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <select id="publicationMonth{{ $doc->id }}" class="form-select" required>
                                                            <option value="">Month</option>
                                                            @foreach(range(1, 12) as $m)
                                                                @php $mVal = str_pad((string) $m, 2, '0', STR_PAD_LEFT); @endphp
                                                                <option value="{{ $mVal }}" {{ $modalMonth === $mVal ? 'selected' : '' }}>{{ \Illuminate\Support\Carbon::create(null, $m, 1)->format('F') }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select id="publicationDay{{ $doc->id }}" class="form-select" required>
                                                            <option value="">Day</option>
                                                            @foreach(range(1, 31) as $d)
                                                                @php $dVal = str_pad((string) $d, 2, '0', STR_PAD_LEFT); @endphp
                                                                <option value="{{ $dVal }}" {{ $modalDay === $dVal ? 'selected' : '' }}>{{ $d }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select id="publicationYear{{ $doc->id }}" class="form-select" required>
                                                            <option value="">Year</option>
                                                            @foreach(range((int) date('Y'), 1980) as $y)
                                                                <option value="{{ $y }}" {{ (string) $modalYear === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="publication_date" id="publicationDate{{ $doc->id }}" value="{{ $modalDate }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ old('doc_id') == $doc->id ? old('title') : $doc->title }}" maxlength="500" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Tags</label>
                                                <input type="text" name="tags" class="form-control" value="{{ old('doc_id') == $doc->id ? old('tags') : $doc->tags }}" maxlength="1000" placeholder="Comma-separated (e.g., AI, Robotics, Education)">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">PDF (leave blank to keep current)</label>
                                                <input type="file" name="pdf" class="form-control" accept="application/pdf">
                                                <small class="text-muted">Maximum file size: 20MB</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var modal = document.getElementById('updateModal{{ $doc->id }}');
                                if (!modal) return;
                                var typeSelect = modal.querySelector('select[name="type"]');
                                var select = modal.querySelector('select[name="mides_category_id"]');

                                function updateDropdown() {
                                    var type = (typeSelect) ? typeSelect.value : '';
                                    var options = (window.midesCategoriesByType && window.midesCategoriesByType[type]) ? window.midesCategoriesByType[type] : [];
                                    var selectedId = select ? select.getAttribute('data-selected') : '';
                                    var html = '<option value="">Select...</option>' + options.map(function(opt) {
                                        var sel = (selectedId && parseInt(selectedId) === opt.id) ? ' selected' : '';
                                        return '<option value="' + opt.id + '"' + sel + '>' + opt.name + '</option>';
                                    }).join('');
                                    if (select) select.innerHTML = html;
                                }

                                function syncModalPublicationDate() {
                                    var monthEl = document.getElementById('publicationMonth{{ $doc->id }}');
                                    var dayEl = document.getElementById('publicationDay{{ $doc->id }}');
                                    var yearEl = document.getElementById('publicationYear{{ $doc->id }}');
                                    var hiddenEl = document.getElementById('publicationDate{{ $doc->id }}');

                                    if (!monthEl || !dayEl || !yearEl || !hiddenEl) return;
                                    var month = monthEl.value;
                                    var day = dayEl.value;
                                    var year = yearEl.value;
                                    hiddenEl.value = (year && month && day) ? (year + '-' + month + '-' + day) : '';
                                }

                                if (typeSelect) typeSelect.addEventListener('change', updateDropdown);
                                var monthSelect = document.getElementById('publicationMonth{{ $doc->id }}');
                                var daySelect = document.getElementById('publicationDay{{ $doc->id }}');
                                var yearSelect = document.getElementById('publicationYear{{ $doc->id }}');
                                if (monthSelect) monthSelect.addEventListener('change', syncModalPublicationDate);
                                if (daySelect) daySelect.addEventListener('change', syncModalPublicationDate);
                                if (yearSelect) yearSelect.addEventListener('change', syncModalPublicationDate);
                                // If modal is shown, update dropdown to match type
                                modal.addEventListener('show.bs.modal', function() {
                                    updateDropdown();
                                    syncModalPublicationDate();
                                });

                                syncModalPublicationDate();
                            });
                        </script>
                        @php
                            $midesCategoriesByType = \App\Models\MidesCategory::all()
                                ->groupBy('type')
                                ->map(function($cats){
                                    return $cats->map(function($c){ return ['id' => $c->id, 'name' => $c->name]; });
                                });
                        @endphp
                        <div id="midesCategoriesData" data-map='@json($midesCategoriesByType)'></div>
                        <script>
                            (function(){
                                var el = document.getElementById('midesCategoriesData');
                                var map = {};
                                if (el && el.dataset && el.dataset.map) {
                                    try { map = JSON.parse(el.dataset.map); } catch(e) { map = {}; }
                                }
                                window.midesCategoriesByType = map;
                            })();
                        </script>
                        </form>
            </div>
        </div>
    </div>
    @endforeach
    </tbody>
    </table>
</div>
</div>
<div class="d-flex justify-content-center mt-4">
    {{ $documents->onEachSide(1)->links('pagination::bootstrap-5') }}


</div>
</div>
</div>
<!-- Details Modal -->
<div class="modal fade" id="docDetailsModal" tabindex="-1" aria-labelledby="docDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header" style="background: linear-gradient(135deg, #f8bbd0, #f48fb1); color: #4a0033;">
                <h5 class="modal-title fw-bold" id="docDetailsModalLabel">Document Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <h3 class="fw-bold mb-1" id="docTitle" style="color:#880e4f;"></h3>
                    <div class="text-muted">By <span id="docAuthor">-</span></div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="small text-uppercase text-muted">Type</div>
                        <div id="docType" class="fw-semibold">-</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-uppercase text-muted">Category/Program</div>
                        <div><span id="docCategory" class="badge bg-secondary-subtle text-dark px-3 py-2"></span></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-uppercase text-muted">Publication Date</div>
                        <div id="docPublicationDate" class="fw-semibold">-</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-uppercase text-muted">Advisor(s)</div>
                        <div id="docAdvisors" class="fw-semibold">-</div>
                    </div>
                    <div class="col-sm-12">
                        <div class="small text-uppercase text-muted">Tags</div>
                        <div id="docTags" class="fw-semibold">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <a id="openMidesPdfBtn" href="#" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-pdf"></i> Open PDF
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button id="updateDocBtn" type="button" class="btn btn-warning">Update</button>
                    <form id="deleteDocForm" action="#" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
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
    .table tbody tr.doc-row:hover {
        background: #fff0f5;
        transform: translateY(-1px);
        transition: all 120ms ease-in-out;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var detailsEl = document.getElementById('docDetailsModal');
        var detailsModal = new bootstrap.Modal(detailsEl);
        var currentUpdateModalId = null;

        // Prevent row click when clicking on action buttons inside the row
        document.querySelectorAll('[data-no-row-click]').forEach(function(el){
            el.addEventListener('click', function(e){
                e.stopPropagation();
            });
        });

        function setText(elId, text) {
            var el = document.getElementById(elId);
            if (el) el.textContent = text || '-';
        }

        document.querySelectorAll('tr.doc-row').forEach(function(row) {
            row.addEventListener('click', function() {
                var title = this.dataset.title || '-';
                var author = this.dataset.author || '-';
                var publicationDate = this.dataset.publicationDate || '-';
                var advisors = this.dataset.advisors || '-';
                var tags = this.dataset.tags || '-';
                var type = this.dataset.type || '-';
                var category = this.dataset.category || '—';
                var pdfUrl = this.dataset.pdfUrl || '#';
                currentUpdateModalId = this.dataset.updateModalId || null;
                var deleteUrl = this.dataset.deleteUrl || '#';

                setText('docTitle', title);
                setText('docAuthor', author);
                setText('docPublicationDate', publicationDate);
                setText('docAdvisors', advisors);
                setText('docTags', tags);
                setText('docType', type);
                setText('docCategory', category);

                var pdfBtn = document.getElementById('openMidesPdfBtn');
                if (pdfBtn) pdfBtn.href = pdfUrl;

                var deleteForm = document.getElementById('deleteDocForm');
                if (deleteForm) deleteForm.action = deleteUrl;

                detailsModal.show();
            });
        });

        var updateBtn = document.getElementById('updateDocBtn');
        if (updateBtn) {
            updateBtn.addEventListener('click', function() {
                if (!currentUpdateModalId) return;
                // Hide details then show update modal
                detailsModal.hide();
                setTimeout(function() {
                    var updEl = document.getElementById(currentUpdateModalId);
                    if (updEl) {
                        var updModal = new bootstrap.Modal(updEl);
                        updModal.show();
                    }
                }, 200);
            });
        }

        // Reopen modal if there are validation errors
        @if($errors->any() && old('doc_id'))
            var errorModalEl = document.getElementById('updateModal{{ old("doc_id") }}');
            if (errorModalEl) {
                var errorModal = new bootstrap.Modal(errorModalEl);
                errorModal.show();
            }
        @endif
    });
</script>
@endsection