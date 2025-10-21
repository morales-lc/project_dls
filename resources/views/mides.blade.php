<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIDES</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>

<body>
    @include('navbar')

    <div class="container py-5">
        <!-- Centered top section -->
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-2" style="color:#e83e8c;">Welcome to MIDES Repository!</h2>
            <div class="fs-5 fw-semibold mb-1" style="color:#e83e8c;">
                The Mides Repository is a digital repository of scholarly and creative works of the faculty, students, and personnel of Lourdes College.
            </div>
            <div class="text-muted small">
                <strong>Graduate Theses</strong> (contains abstracts, introduction, and related literature of the theses completed for the M.A. programs in Lourdes College)<br>
                <strong>Undergraduate Baby Thesis</strong> (contains abstracts, introduction, and related literature of the undergraduate theses)
            </div>
        </div>

        <!-- Search form -->
        <form class="mb-4" method="GET" action="{{ route('mides.search') }}">
            <!-- Search bar -->
            <div class="row justify-content-center mb-3">
                <div class="col-12 col-md-8">
                    <input
                        type="search"
                        name="q"
                        class="form-control"
                        placeholder="digital library system"
                        aria-label="Search"
                        value="{{ $search ?? '' }}">
                </div>
                <div class="col-12 col-md-auto mt-2 mt-md-0 text-center">
                    <button class="btn btn-dark w-100 w-md-auto" type="submit">Search</button>
                </div>
            </div>

            <!-- Dropdowns below search bar -->
            <div class="row justify-content-center g-2 g-md-3">
                <div class="col-12 col-md-3">
                    <select class="form-select" name="type" id="mides-type-select">
                        <option value="">SELECT TYPE</option>
                        @if(isset($types))
                            @foreach($types as $t)
                                <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-12 col-md-3" id="mides-program-col" style="display: none;">
                    <select class="form-select" name="program" id="mides-program-select">
                        <option value="">SELECT PROGRAM</option>
                        @if(isset($programs))
                            @foreach($programs as $p)
                                <option value="{{ $p }}" {{ (isset($program) && $program == $p) ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-12 col-md-3" id="mides-year-col">
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="\d{4}"
                        maxlength="4"
                        class="form-control"
                        name="year"
                        placeholder="ENTER YEAR (e.g., 2024)"
                        value="{{ request('year') ?? '' }}"
                        aria-label="Filter by year"
                    >
                </div>
            </div>
        </form>

        <!-- JS for dynamic program dropdown -->
        <script>
            (function() {
                function updateProgramDropdown(type) {
                    var col = document.getElementById('mides-program-col');
                    var progSel = document.getElementById('mides-program-select');
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
                                        if (typeof p === 'object' && p !== null) {
                                            opt.value = p.id;
                                            opt.textContent = p.name;
                                        } else {
                                            opt.value = p;
                                            opt.textContent = p;
                                        }
                                        progSel.appendChild(opt);
                                    });
                                }
                            }
                        });
                }

                var typeSelect = document.getElementById('mides-type-select');
                if (typeSelect) {
                    typeSelect.addEventListener('change', function() {
                        updateProgramDropdown(typeSelect.value);
                    });
                    document.addEventListener('DOMContentLoaded', function() {
                        updateProgramDropdown(typeSelect.value);
                    });
                }
            })();
        </script>

        <!-- Categories Section -->
        <div class="mt-5 row g-4 justify-content-center">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.graduate.categories') }}" class="text-decoration-none mides-card-link mides-card--grad">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <span class="display-4 text-primary"><i class="bi bi-journal-bookmark"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Graduate Theses</h5>
                            <div class="text-muted small">Masters and related research</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.undergrad.programs') }}" class="text-decoration-none mides-card-link mides-card--undergrad">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <span class="display-4 text-danger"><i class="bi bi-journal-text"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Undergraduate Baby Thesis</h5>
                            <div class="text-muted small">Undergraduate research</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.faculty_theses') }}" class="text-decoration-none mides-card-link mides-card--faculty">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <span class="display-4 text-info"><i class="bi bi-person-badge"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Faculty/Theses/Dissertations</h5>
                            <div class="text-muted small">Faculty publications, theses, and dissertations</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.seniorhigh.programs') }}" class="text-decoration-none mides-card-link mides-card--shs">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <span class="display-4 text-success"><i class="bi bi-mortarboard"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Senior High School Research Paper</h5>
                            <div class="text-muted small">ABM, HUMSS, STEM, TVL, ICT, Culinary Arts</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 200px; display: inline-block;"></div>

    <!-- PDF Modal Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var previewModal = document.getElementById('previewModal');
            if (previewModal) {
                previewModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var pdfUrl = button.getAttribute('data-pdf');
                    var title = button.getAttribute('data-title');
                    var author = button.getAttribute('data-author');
                    var year = button.getAttribute('data-year');
                    document.getElementById('pdfFrame').src = pdfUrl;
                    document.getElementById('pdfInfo').innerHTML = `<strong>Title:</strong> ${title}<br><strong>Author:</strong> ${author}<br><strong>Year:</strong> ${year}`;
                });
                previewModal.addEventListener('hidden.bs.modal', function() {
                    document.getElementById('pdfFrame').src = '';
                    document.getElementById('pdfInfo').innerHTML = '';
                });
            }
        });
    </script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @include('footer')
</body>

</html>
