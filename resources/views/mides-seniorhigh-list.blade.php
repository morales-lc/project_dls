<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $program }} Research Papers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/mides.css') }}">
    
</head>
<body>
@include('navbar')
<div class="container py-5">
    <div class="card shadow-lg w-100 border-0" style="max-width:1100px; margin:auto;">
        <div class="card-header bg-pink d-flex align-items-center">
            <i class="bi bi-journal-text fs-3 me-2"></i>
            <span class="fw-bold fs-5">{{ $program }} Research Papers</span>
            <div class="ms-auto">
                <a href="{{ route('mides.seniorhigh.programs') }}" class="btn btn-outline-secondary me-2">&#8592; Back to Programs</a>
                <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-pink">MIDES Dashboard</a>
            </div>
        </div>
        <div class="card-body">
            <form class="row g-2 mb-4" method="GET" action="">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by title, author, year..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="year" {{ request('sort') == 'year' ? 'selected' : '' }}>Year</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Author</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="direction" class="form-select">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-pink w-100">Filter/Search</button>
                </div>
            </form>

            @if($records->isEmpty())
                <div class="alert alert-info">No records found for this program.</div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th class="text-center">PDF</th>
                            <th class="text-center">Bookmark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link p-0 text-start text-decoration-none" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $record->id }}">{{ $record->title }}</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-link p-0 text-start text-decoration-none" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $record->id }}">{{ $record->author }}</button>
                            </td>
                            <td>{{ $record->year }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-pink btn-sm view-btn" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $record->id }}"><i class="bi bi-file-earmark-pdf"></i> View</button>

                                <div class="modal fade" id="pdfModal{{ $record->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $record->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="pdfModalLabel{{ $record->id }}">{{ $record->title }} ({{ $record->year }})</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <iframe src="{{ route('mides.seniorhigh.viewer', $record->id) }}" width="100%" height="100%" style="border:none; min-height:70vh;"></iframe>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details Modal -->
                                <div class="modal fade" id="detailsModal{{ $record->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $record->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title" id="detailsModalLabel{{ $record->id }}"><i class="bi bi-file-text me-2"></i>Document Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-3">Title</dt>
                                                    <dd class="col-sm-9 text-break">{{ $record->title }}</dd>

                                                    <dt class="col-sm-3">Author</dt>
                                                    <dd class="col-sm-9 text-break">{{ $record->author }}</dd>

                                                    <dt class="col-sm-3">Year</dt>
                                                    <dd class="col-sm-9">{{ $record->year }}</dd>

                                                    <dt class="col-sm-3">Type</dt>
                                                    <dd class="col-sm-9">{{ $record->type ?? 'Senior High School Research Paper' }}</dd>

                                                    <dt class="col-sm-3">Program</dt>
                                                    <dd class="col-sm-9">{{ $program }}</dd>
                                                </dl>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-outline-pink" target="_blank" href="{{ route('mides.seniorhigh.viewer', $record->id) }}"><i class="bi bi-file-earmark-pdf"></i> Open PDF</a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @auth
                                    @php
                                        $sf = optional(auth()->user()->studentFaculty);
                                        $isBookmarked = false;
                                        if ($sf && $sf->id) {
                                            $isBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                                                ->where('bookmarkable_type', \App\Models\MidesDocument::class)
                                                ->where('bookmarkable_id', $record->id)
                                                ->exists();
                                        }
                                    @endphp
                                    @if($sf && $sf->id)
                                        <form method="POST" action="{{ route('bookmarks.toggle') }}" class="d-inline bookmark-toggle">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $record->id }}">
                                            <input type="hidden" name="type" value="mides">
                                            <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} bookmark-btn"><i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i> <span class="bookmark-text">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span></button>
                                        </form>
                                    @endif
                                @endauth
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12 text-center">
            <a href="{{ route('mides.seniorhigh.programs') }}" class="btn btn-outline-dark">Back to Programs</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
<div style="height: 100px;"></div>
@include('footer')
</html>
<script>
    // Generic bookmark toggle handler (AJAX)
    (function(){
        async function handleToggle(e){
            e.preventDefault();
            const form = e.target.closest('.bookmark-toggle') || e.target;
            if (!form) return;
            const btn = form.querySelector('.bookmark-btn');
            const icon = btn.querySelector('i');
            const textEl = btn.querySelector('.bookmark-text');
            const formData = new FormData(form);
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

            try{
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value
                    },
                    body: formData
                });

                if (!resp.ok) throw new Error('Network response not ok');
                const data = await resp.json();
                if (data.status === 'bookmarked'){
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-pink');
                    if (icon) icon.className = 'bi bi-bookmark-fill';
                    if (textEl) textEl.textContent = 'Bookmarked';
                } else {
                    btn.classList.remove('btn-pink');
                    btn.classList.add('btn-outline-warning');
                    if (icon) icon.className = 'bi bi-bookmark';
                    if (textEl) textEl.textContent = 'Bookmark';
                }
            }catch(err){
                console.error('Bookmark toggle failed', err);
            }finally{
                btn.disabled = false;
                if (btn.innerHTML.includes('spinner-border')){
                    btn.innerHTML = originalHtml;
                }
            }
        }

        document.addEventListener('submit', function(e){
            if (e.target && e.target.classList && e.target.classList.contains('bookmark-toggle')){
                handleToggle(e);
            }
        });
    })();
</script>