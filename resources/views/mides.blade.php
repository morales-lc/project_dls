<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mides</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
</head>

<body>
    @include('navbar')
    <div class="container py-5">
        <h2 class="mb-2 fw-bold">Welcome to MIDES repository!</h2>
        <div class="mb-3 text-secondary">
            <em>The Mides Repository is a digital repository of scholarly and creative works of the faculty, students and personnel of Lourdes College.</em>
        </div>
        <div class="mb-2 text-muted">
            <strong>Graduate Theses</strong> (contains abstracts, introduction and related literature of the theses completed for the M.A. programs in Lourdes College)<br>
            <strong>Undergraduate Baby Thesis</strong> (contains abstracts, introduction and related literature of the undergraduate theses)
        </div>
        <form class="d-flex mb-4" method="GET" action="{{ route('mides.search') }}">
            <input class="form-control me-2" type="search" name="q" placeholder="digital library system" aria-label="Search">
            <select class="form-select me-2" name="type">
                <option value="">SELECT TYPE</option>
                @if(isset($types))
                @foreach($types as $type)
                <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
                @endif
            </select>
            <button class="btn btn-dark" type="submit">Search</button>
        </form>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.graduate.categories') }}" class="text-decoration-none mides-card-link">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body">
                            <span class="display-4 text-primary"><i class="bi bi-journal-bookmark"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Graduate Theses</h5>
                            <div class="text-muted small">Masters and related research</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.undergrad.programs') }}" class="text-decoration-none mides-card-link">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body">
                            <span class="display-4 text-danger"><i class="bi bi-journal-text"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Undergraduate Baby Thesis</h5>
                            <div class="text-muted small">Undergraduate research</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.faculty_theses') }}" class="text-decoration-none mides-card-link">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body">
                            <span class="display-4 text-info"><i class="bi bi-person-badge"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Faculty/Theses/Dissertations</h5>
                            <div class="text-muted small">Faculty publications, theses, and dissertations</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('mides.seniorhigh.programs') }}" class="text-decoration-none mides-card-link">
                    <div class="card h-100 mides-card-hover shadow-sm text-center border-0">
                        <div class="card-body">
                            <span class="display-4 text-success"><i class="bi bi-mortarboard"></i></span>
                            <h5 class="mt-3 mb-1 fw-bold">Senior High School Research Paper</h5>
                            <div class="text-muted small">ABM, HUMSS, STEM, TVL, ICT, Culinary Arts</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var previewModal = document.getElementById('previewModal');
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
        });
    </script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


</body>

</html>