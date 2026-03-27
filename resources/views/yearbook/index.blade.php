<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearbook Archive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        .yearbook-shell {
            max-width: 1200px;
        }

        .yearbook-title {
            color: #343a40;
            letter-spacing: 0.5px;
        }

        .archive-search {
            max-width: 600px;
            margin: 0 auto;
        }

        .decade-toggle {
            background: #fff;
            font-weight: 600;
            color: #343a40;
        }

        .decade-toggle:not(.collapsed) {
            color: #d81b60;
            background: #fff7fb;
            box-shadow: inset 0 -2px 0 rgba(216, 27, 96, 0.25);
        }

        .yearbook-item {
            border: 1px solid #f1d7e5;
            border-radius: 0.8rem;
            background: #fff;
        }

        .yearbook-item + .yearbook-item {
            margin-top: 0.65rem;
        }
    </style>
</head>
<body style="min-height: 100vh; background: #f8f9fa;">
@include('navbar')

<div class="container yearbook-shell py-5 mb-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold yearbook-title mb-3">
            <i class="bi bi-collection-fill me-2" style="color:#d81b60;"></i>
            Yearbook Archive
        </h1>

        <form method="GET" action="{{ route('yearbook.index') }}" class="archive-search">
            <div class="input-group input-group-lg shadow-sm">
                <input
                    type="search"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="form-control"
                    placeholder="Search year or title..."
                >
                <button class="btn btn-dark" type="submit">Search</button>
                @if(!empty($search))
                    <a href="{{ route('yearbook.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    @if($groupedByDecade->isEmpty())
        <div class="alert alert-info shadow-sm border-0">No yearbooks found.</div>
    @else
        <div class="accordion" id="yearbookDecadesAccordion">
            @foreach($groupedByDecade as $decade => $items)
                @php
                    $panelId = 'decade-' . str_replace('-', '', $decade);
                    $isOpen = false;
                @endphp
                <div class="accordion-item mb-2 border-0 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="heading-{{ $panelId }}">
                        <button
                            class="accordion-button decade-toggle {{ $isOpen ? '' : 'collapsed' }}"
                            type="button"
                            data-yearbook-target="#{{ $panelId }}"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                            aria-controls="{{ $panelId }}"
                        >
                            {{ str_replace('-', '–', $decade) }}
                        </button>
                    </h2>
                    <div
                        id="{{ $panelId }}"
                        class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}"
                        aria-labelledby="heading-{{ $panelId }}"
                    >
                        <div class="accordion-body bg-light">
                            @foreach($items as $yearbook)
                                <div class="yearbook-item px-3 py-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold">{{ $yearbook->title }}</div>
                                        
                                    </div>
                                    <a
                                        href="{{ asset('storage/' . $yearbook->pdf_file) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-sm btn-outline-danger mt-2 mt-md-0"
                                    >
                                        <i class="bi bi-file-earmark-pdf"></i> Open PDF
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@include('footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.decade-toggle[data-yearbook-target]').forEach(function (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const targetSelector = toggleBtn.getAttribute('data-yearbook-target');
                const target = document.querySelector(targetSelector);
                if (!target) return;

                const collapse = bootstrap.Collapse.getOrCreateInstance(target, { toggle: false });
                const isOpen = target.classList.contains('show');

                if (isOpen) {
                    collapse.hide();
                    toggleBtn.classList.add('collapsed');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                } else {
                    collapse.show();
                    toggleBtn.classList.remove('collapsed');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });
</script>
</body>
</html>
