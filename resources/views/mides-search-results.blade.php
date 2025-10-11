<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        .modal-open {
            overflow: hidden !important;
        }

        /* Card hover animation and improved palette */
        .card.h-100 {
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
            border-radius: 0.85rem;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .card.h-100:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 10px 30px rgba(216, 27, 96, 0.14), 0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: #d81b60; /* Pink highlight border */
        }

        .view-btn {
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-outline-warning {
            border-color: #ffd1e3 !important;
            color: #d81b60 !important;
            background: white;
        }

        .btn-outline-warning:hover {
            background: #ffd1e3 !important;
            color: #8b0f3a !important;
        }

        .btn-pink {
            background: #e83e8c !important;
            color: #fff !important;
            border: none !important;
        }

        .bookmark-btn {
            white-space: nowrap;
        }

        .card-title {
            color: #2b2b2b;
        }

        .card-body {
            background: linear-gradient(180deg, #fff 92%, #fff7fb 100%);
        }

        .small .text-muted {
            color: #6c757d !important;
        }

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
</head>

<body>
    @include('navbar')

    <div class="container py-4">
        <h2 class="fw-bold mb-3 text-center text-md-start">Search Results</h2>
        <div class="mt-4 text-center">
            <a href="{{ route('mides.dashboard') }}" class="btn btn-secondary">Back to MIDES Dashboard</a>
        </div>
        <div style="height: 20px;"></div>

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
                            if (typeLower.indexOf('faculty') !== -1 || typeLower.indexOf('dissertation') !== -1) {
                                col.style.display = 'none';
                                if (progSel) progSel.value = '';
                                return;
                            }
                            col.style.display = 'block';
                            fetch('/mides/programs?type=' + encodeURIComponent(type))
                                .then(resp => resp.json())
                                .then(data => {
                                    if (progSel) {
                                        progSel.innerHTML = '<option value="">SELECT PROGRAM</option>';
                                        if (Array.isArray(data.programs)) {
                                            data.programs.forEach(p => {
                                                const opt = document.createElement('option');
                                                opt.value = p;
                                                opt.textContent = p;
                                                progSel.appendChild(opt);
                                            });
                                        }
                                    }
                                });
                        }
                        const t2 = document.getElementById('mides-type-select-2');
                        if (t2) {
                            t2.addEventListener('change', () => updateProgramDropdown2(t2.value));
                            document.addEventListener('DOMContentLoaded', () => updateProgramDropdown2(t2.value));
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
                <div class="card h-100 shadow-sm"> <!-- Removed border-0 -->
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
                        <a href="{{ route('mides.search.viewer', $doc->id) }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-sm btn-outline-secondary ms-2" style="border-radius:8px; align-self:center;">
                            <i class="bi bi-box-arrow-up-right"></i> Open
                        </a>

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
                            <button type="submit"
                                class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-warning' }} bookmark-btn flex-fill">
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
    </div>
    <div style="height: 200px;"></div>
    @include('footer')
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.bookmark-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('button');
                if (!btn) return;
                var originalHTML = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ...';
                var fd = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                    },
                    body: fd
                }).then(res => res.json())
                    .then(data => {
                        if (!data) throw new Error('No response');
                        if (data.status === 'bookmarked') {
                            btn.classList.remove('btn-outline-warning');
                            btn.classList.add('btn-pink');
                            btn.innerHTML = '<i class="bi bi-bookmark-fill"></i> Bookmarked';
                        } else if (data.status === 'removed') {
                            btn.classList.remove('btn-pink');
                            btn.classList.add('btn-outline-warning');
                            btn.innerHTML = '<i class="bi bi-bookmark"></i> Bookmark';
                        } else {
                            btn.innerHTML = originalHTML;
                        }
                    }).catch(err => {
                        console.error(err);
                        btn.innerHTML = originalHTML;
                        alert('Bookmark action failed.');
                    }).finally(() => {
                        btn.disabled = false;
                    });
            });
        });
    });
</script>
