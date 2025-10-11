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
            <a href="{{ route('mides.upload') }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.05rem;"><i class="bi bi-plus-lg"></i> Add New Document</a>
            <div style="height: 30px;"></div>
            <form method="GET" action="{{ route('mides.management') }}" class="row g-2 mb-3 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by title, author, year..." value="{{ $search ?? '' }}">
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
                        <option value="year" {{ $sort == 'year' ? 'selected' : '' }}>Year</option>
                        <option value="author" {{ $sort == 'author' ? 'selected' : '' }}>Author</option>
                        <option value="title" {{ $sort == 'title' ? 'selected' : '' }}>Title</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('mides.management') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>

            <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle bg-white rounded-4">
                        <thead class="table-pink">
                            <tr>
                                <th>Type</th>
                                <th>Category/Program</th>
                                <th>Author</th>
                                <th>Year</th>
                                <th>Title</th>
                                <th>PDF</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $doc)
                            <tr>
                                <td>{{ $typeNames[$doc->type] ?? $doc->type }}</td>
                                <td>{{ optional($doc->midesCategory)->name ?? $doc->category ?? $doc->program ?? '—' }}</td>
                                <td>{{ $doc->author }}</td>
                                <td>{{ $doc->year }}</td>
                                <td>{{ $doc->title }}</td>
                                <td><a href="{{ asset('storage/' . $doc->pdf_path) }}" target="_blank">View PDF</a></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal{{ $doc->id }}">Update</button>
                                    <form method="POST" action="{{ route('mides.delete', $doc->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateModalLabel{{ $doc->id }}">Update Document</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label class="form-label">Type</label>
                                                    <select name="type" class="form-select" required>
                                                        @foreach($types as $t)
                                                            <option value="{{ $t }}" {{ ($doc->type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Category/Program</label>
                                                    <select name="mides_category_id" class="form-select" id="midesCategorySelect{{ $doc->id }}" data-selected="{{ $doc->mides_category_id ?? '' }}">
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
                                                    <label class="form-label">Author</label>
                                                    <input type="text" name="author" class="form-control" value="{{ $doc->author }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Year</label>
                                                    <input type="number" name="year" class="form-control" value="{{ $doc->year }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" name="title" class="form-control" value="{{ $doc->title }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">PDF (leave blank to keep current)</label>
                                                    <input type="file" name="pdf" class="form-control" accept="application/pdf">
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

                                    if (typeSelect) typeSelect.addEventListener('change', updateDropdown);
                                    // If modal is shown, update dropdown to match type
                                    modal.addEventListener('show.bs.modal', function() { updateDropdown(); });
                                });
                            </script>
                            <script>
                                // global map of type => [{id, name}, ...] encoded as JSON to avoid blade loops
                                window.midesCategoriesByType = {!! \App\Models\MidesCategory::all()->groupBy('type')->map(function($cats){ return $cats->map(function($c){ return ['id' => $c->id, 'name' => $c->name]; }); })->toJson() !!};
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
                {{ $documents->links() }}
            </div>
        </div>
    </div>
@endsection