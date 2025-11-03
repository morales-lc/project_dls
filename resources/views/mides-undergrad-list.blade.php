<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $program }} Theses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/mides.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>

<body>
    @include('navbar')

    <div class="container py-5">
        <div class="card shadow-lg w-100 border-0" style="max-width:1100px; margin:auto;">
            <div class="card-header bg-pink d-flex align-items-center" style="border-radius:1.25rem 1.25rem 0 0;">
                <i class="bi bi-journal-bookmark fs-3 me-2"></i>
                <span class="fw-bold fs-5">{{ $program }} Theses</span>
                <div class="ms-auto">
                    <a href="{{ route('mides.undergrad') }}" class="btn btn-outline-secondary me-2">&#8592; Back to Categories</a>
                    <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-pink">MIDES Dashboard</a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search & Filter Form -->
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

                <!-- Table Results -->
                @if($documents->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Author</th>
                                <th>Year</th>
                                <th>Title</th>
                                <th class="text-center">PDF</th>
                                @if(Auth::check() && Auth::user()->role !== 'guest')
                                <th class="text-center">Bookmark</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-link p-0 text-start text-decoration-none" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $doc->id }}">{{ $doc->author }}</button>
                                </td>
                                <td>{{ $doc->year }}</td>
                                <td>
                                    <button type="button" class="btn btn-link p-0 text-start text-decoration-none" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $doc->id }}">{{ $doc->title }}</button>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-pink btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $doc->id }}">
                                        <i class="bi bi-file-earmark-pdf"></i> View
                                    </button>

                                    <!-- PDF Modal -->
                                    <div class="modal fade" id="pdfModal{{ $doc->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $doc->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:95vw; width:95vw;">
                                            <div class="modal-content" style="height:90vh;">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="pdfModalLabel{{ $doc->id }}">{{ $doc->title }} ({{ $doc->year }})</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <iframe data-src="{{ route('mides.undergrad.viewer', $doc->id) }}" src="about:blank" width="100%" height="100%" style="border:none; min-height:85vh;"></iframe>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Details Modal -->
                                    <div class="modal fade" id="detailsModal{{ $doc->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $doc->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title" id="detailsModalLabel{{ $doc->id }}"><i class="bi bi-file-text me-2"></i>Document Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-3">Title</dt>
                                                        <dd class="col-sm-9 text-break">{{ $doc->title }}</dd>

                                                        <dt class="col-sm-3">Author</dt>
                                                        <dd class="col-sm-9 text-break">{{ $doc->author }}</dd>

                                                        <dt class="col-sm-3">Year</dt>
                                                        <dd class="col-sm-9">{{ $doc->year }}</dd>

                                                        <dt class="col-sm-3">Type</dt>
                                                        <dd class="col-sm-9">{{ $doc->type ?? 'Undergraduate Baby Theses' }}</dd>

                                                        <dt class="col-sm-3">Program</dt>
                                                        <dd class="col-sm-9">{{ $program }}</dd>
                                                    </dl>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-outline-pink" target="_blank" href="{{ route('mides.undergrad.viewer', $doc->id) }}"><i class="bi bi-file-earmark-pdf"></i> Open PDF</a>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @if(Auth::check() && Auth::user()->role !== 'guest')
                                <td class="text-center">
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
                                    <form method="POST" action="{{ route('bookmarks.toggle') }}" class="d-inline bookmark-toggle">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $doc->id }}">
                                        <input type="hidden" name="type" value="mides">
                                        <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} bookmark-btn">
                                            <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                                            <span class="bookmark-text">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $documents->links() }}
                </div>
                @else
                <div class="alert alert-info">No theses found for this program.</div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lazy-load iframe src for PDF modals to avoid calling viewer route on page load
        document.querySelectorAll('.modal').forEach(modalEl => {
            modalEl.addEventListener('show.bs.modal', function(e) {
                const iframe = this.querySelector('iframe[data-src]');
                if (iframe && (!iframe.src || iframe.src === 'about:blank')) {
                    iframe.src = iframe.getAttribute('data-src');
                }
            });
            modalEl.addEventListener('hidden.bs.modal', function(e) {
                const iframe = this.querySelector('iframe[data-src]');
                if (iframe) {
                    iframe.src = 'about:blank';
                }
            });
        });
    </script>
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
                // show spinner state
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
                    // Expected { status: 'bookmarked' } or { status: 'removed' }
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

    <div style="height: 100px;"></div>
    @include('footer')
</body>

</html>