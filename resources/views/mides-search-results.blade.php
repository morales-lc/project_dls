@include('navbar')

<div class="container py-4">
    <h2 class="fw-bold mb-3 text-center text-md-start">Search Results</h2>

    <!-- Responsive Search Form -->
    <div class="mb-4">
        <form class="row g-2" method="GET" action="{{ route('mides.search') }}">
            <div class="col-12 col-md-5">
                <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search again..." aria-label="Search">
            </div>
            <div class="col-12 col-md-3">
                <select class="form-select" name="type" id="mides-type-select-2">
                    <option value="">SELECT TYPE</option>
                    @if(isset($types))
                    @foreach($types as $t)
                    <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-md-3" id="mides-program-col-2" style="display:none; min-width:180px;">
                <select class="form-select" name="program" id="mides-program-select-2">
                    <option value="">SELECT PROGRAM</option>
                    @if(isset($programs))
                    @foreach($programs as $p)
                    <option value="{{ $p }}" {{ (isset($program) && $program == $p) ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-md-2">
                <select class="form-select" name="year">
                    <option value="">SELECT YEAR</option>
                    @if(isset($years))
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ (request('year') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-md-1 d-grid">
                <button class="btn btn-dark" type="submit">Search</button>
            </div>
            <script>
                (function() {
                    function updateProgramDropdown2(type) {
                        var col = document.getElementById('mides-program-col-2');
                        var progSel = document.getElementById('mides-program-select-2');
                        var typeLower = (type || '').toString().toLowerCase();
                        // Hide for Faculty/Theses/Dissertations
                        if (typeLower.indexOf('faculty') !== -1 || typeLower.indexOf('dissertation') !== -1) {
                            col.style.display = 'none';
                            if (progSel) progSel.value = '';
                            return;
                        }
                        // Show for other types and fetch programs
                        col.style.display = 'block';
                        fetch('/mides/programs?type=' + encodeURIComponent(type))
                            .then(function(resp) {
                                return resp.json();
                            })
                            .then(function(data) {
                                if (progSel) {
                                    progSel.innerHTML = '<option value="">SELECT PROGRAM</option>';
                                    if (Array.isArray(data.programs)) {
                                        data.programs.forEach(function(p) {
                                            var opt = document.createElement('option');
                                            opt.value = p;
                                            opt.textContent = p;
                                            progSel.appendChild(opt);
                                        });
                                    }
                                }
                            });
                    }
                    var t2 = document.getElementById('mides-type-select-2');
                    if (t2) {
                        t2.addEventListener('change', function() {
                            updateProgramDropdown2(t2.value);
                        });
                        document.addEventListener('DOMContentLoaded', function() {
                            updateProgramDropdown2(t2.value);
                        });
                    }
                })();
            </script>
        </form>
    </div>

    <!-- Search Results -->
    @if($documents->count())
    <div class="row g-4">
        @foreach($documents as $doc)
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-semibold text-truncate" title="{{ $doc->title }}">{{ $doc->title }}</h5>
                    <div class="small mb-2"><span class="text-muted">Author:</span> {{ $doc->author }}</div>
                    <div class="small mb-2"><span class="text-muted">Year:</span> {{ $doc->year }}</div>
                    <div class="small mb-2"><span class="text-muted">Type:</span> {{ $doc->type }}</div>
                    @if($doc->category)
                    <div class="small mb-2"><span class="text-muted">Category:</span> {{ $doc->category }}</div>
                    @endif
                    @if($doc->program)
                    <div class="small mb-2"><span class="text-muted">Program:</span> {{ $doc->program }}</div>
                    @endif
                </div>
                <div class="card-footer bg-white border-0 d-flex flex-column flex-sm-row gap-2">
                    <!-- View Button (Modal) -->
                    <button
                        type="button"
                        class="flex-fill view-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#pdfModal{{ $doc->id }}"
                        style="border: 1.5px solid #e83e8c; color: #e83e8c; background: transparent; padding: 8px 14px; border-radius: 8px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.25s ease; width: 100%;"
                        onmouseover="this.style.background='#e83e8c'; this.style.color='white';"
                        onmouseout="this.style.background='transparent'; this.style.color='#e83e8c';">
                        <i class="bi bi-file-earmark-pdf"></i> View
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="pdfModal{{ $doc->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $doc->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="pdfModalLabel{{ $doc->id }}">{{ $doc->title }} ({{ $doc->year }})</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <iframe src="{{ asset('storage/' . $doc->pdf_path) }}" width="100%" height="100%" style="border:none; min-height:70vh;"></iframe>
                            </div>
                        </div>
                    </div>
                    <!-- Bookmark Button -->
                    @auth
                    @php
                    $sf = optional(auth()->user()->studentFaculty);
                    $isBookmarked = false;
                    if ($sf && $sf->id) {
                    $isBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                    ->where('bookmarkable_type', \App\Models\MidesDocument::class)
                    ->where('bookmarkable_id', $doc->id)
                    ->exists();
                    }
                    @endphp
                    @if($sf && $sf->id)
                    <form method="POST" action="{{ route('bookmarks.toggle') }}" class="d-inline bookmark-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $doc->id }}">
                        <input type="hidden" name="type" value="mides">
                        <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} bookmark-btn flex-fill">
                            <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                            {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                        </button>
                    </form>
                    @endif
                    @endauth

                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $documents->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="alert alert-info text-center">No results found for your search.</div>
    @endif

    <div class="mt-4 text-center">
        <a href="{{ route('mides.dashboard') }}" class="btn btn-secondary">Back to MIDES Dashboard</a>
    </div>
</div>

@push('styles')
<style>
    @media (max-width: 768px) {
        form.row {
            flex-direction: column;
        }

        .form-control,
        .form-select,
        .btn {
            width: 100%;
        }
    }
</style>
@endpush