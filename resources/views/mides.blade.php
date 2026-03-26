<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIDES</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/mides.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mides-scholar.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        .mides-search-panel {
            border: 1px solid #f5cada;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, #fff 0%, #fff6fa 100%);
            box-shadow: 0 12px 28px rgba(232, 62, 140, 0.12);
            padding: 1rem;
        }

        .tag-discovery-card {
            margin-top: .85rem;
            padding: .85rem;
            border-radius: .95rem;
            border: 2px solid #f28bb8;
            background: linear-gradient(145deg, #fff7fc 0%, #fff 100%);
            box-shadow: 0 6px 20px rgba(232, 62, 140, 0.14);
        }

        .tag-toggle-btn {
            border: none;
            color: #fff;
            background: #e83e8c;
            font-weight: 700;
            border-radius: 999px;
            padding: .4rem .9rem;
        }

        .tag-filter-box {
            display: block;
            margin-top: .75rem;
            padding: .75rem;
            border-radius: .9rem;
            border: 1px solid #f4bfd5;
            background: #fff;
        }

        .suggested-tags {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .suggested-tag-btn {
            border: 1px solid #f2a8ca;
            background: #fff;
            color: #9a1e5d;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            padding: .2rem .65rem;
        }

        .suggested-tag-btn:hover {
            background: #ffe2ef;
        }

        .tag-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            background: #ffd9e9;
            color: #7a0f42;
            font-size: .85rem;
            font-weight: 600;
            margin: .2rem;
        }

        .tag-chip button {
            border: none;
            background: transparent;
            color: #7a0f42;
            font-size: .95rem;
            line-height: 1;
        }
    </style>
</head>

<body class="mides-scholar">
    @include('navbar')

    <div class="container py-5 mides-wrap">
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
        <form class="mb-4 mides-search-form" method="GET" action="{{ route('mides.search') }}" id="midesSearchForm">
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
            <div class="row justify-content-center g-2">
                <div class="col-12 col-md-4">
                    <select class="form-select" name="type" id="mides-type-select">
                        <option value="">SELECT TYPE</option>
                        @if(isset($types))
                            @foreach($types as $t)
                                <option value="{{ $t }}" {{ (isset($type) && $type == $t) ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-12 col-md-4" id="mides-program-col" style="display: {{ (isset($type) && $type && !str_contains(strtolower($type), 'faculty') && !str_contains(strtolower($type), 'dissertation')) ? 'block' : 'none' }};">
                    <select class="form-select" name="program" id="mides-program-select">
                        <option value="">SELECT PROGRAM</option>
                        @if(isset($programs))
                            @foreach($programs as $p)
                                <option value="{{ $p }}" {{ (isset($program) && $program == $p) ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <input
                        type="date"
                        class="form-control"
                        name="publication_date"
                        value="{{ request('publication_date') ?? '' }}"
                        max="{{ date('Y-m-d') }}"
                        aria-label="Filter by publication date"
                    >
                </div>
            </div>

            <div class="mides-tag-box">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="fw-bold" style="color:#a31358;"><i class="bi bi-stars"></i> Tag Discovery</div>
                        <div class="small text-muted">Add one or more tags to narrow down MIDES results faster.</div>
                    </div>
                    <button type="button" class="btn tag-toggle-btn" id="toggleTagSearch">
                        <i class="bi bi-tags"></i> Hide Tag Search
                    </button>
                </div>
                <div id="tagFilterBox" class="tag-filter-box">
                    <label for="tagInput" class="form-label fw-semibold mb-1">Type a tag and press Enter</label>
                    <input type="text" id="tagInput" class="form-control" list="midesTagSuggestions" placeholder="e.g. artificial intelligence">
                    <datalist id="midesTagSuggestions">
                        @foreach(($tagSuggestions ?? []) as $suggestion)
                            <option value="{{ $suggestion }}"></option>
                        @endforeach
                    </datalist>
                    <div class="small text-muted mt-2 mb-1">Suggested tags:</div>
                    <div class="suggested-tags" id="suggestedTags">
                        @foreach(array_slice(($tagSuggestions ?? []), 0, 8) as $suggestion)
                            <button type="button" class="suggested-tag-btn" data-tag="{{ $suggestion }}">{{ $suggestion }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="tags" id="hiddenTags" value="{{ isset($tagFilters) ? implode(',', $tagFilters) : request('tags', '') }}">
                    <div id="selectedTags" class="mt-2"></div>
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

                    // Hide for Faculty/Theses/Dissertations or when no type selected
                    if (!type || typeLower.indexOf('faculty') !== -1 || typeLower.indexOf('dissertation') !== -1) {
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
                                // Store current selection
                                var currentValue = progSel.value;
                                progSel.innerHTML = '<option value="">SELECT PROGRAM</option>';
                                if (Array.isArray(data.programs)) {
                                    data.programs.forEach(function(prog) {
                                        var opt = document.createElement('option');
                                        // Always use name as value for backward compatibility
                                        opt.value = prog.name;
                                        opt.textContent = prog.name;
                                        // Restore selection if it matches
                                        if (currentValue && prog.name == currentValue) {
                                            opt.selected = true;
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
                    // Initialize on page load
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', function() {
                            updateProgramDropdown(typeSelect.value);
                        });
                    } else {
                        updateProgramDropdown(typeSelect.value);
                    }
                }
            })();

            (function() {
                var toggleBtn = document.getElementById('toggleTagSearch');
                var tagBox = document.getElementById('tagFilterBox');
                var tagInput = document.getElementById('tagInput');
                var selectedTagsEl = document.getElementById('selectedTags');
                var suggestedTagsEl = document.getElementById('suggestedTags');
                var hiddenTags = document.getElementById('hiddenTags');
                var tagList = [];

                function renderTags() {
                    selectedTagsEl.innerHTML = '';
                    tagList.forEach(function(tag, idx) {
                        var chip = document.createElement('span');
                        chip.className = 'tag-chip';
                        chip.innerHTML = '<span>' + tag + '</span><button type="button" data-idx="' + idx + '">&times;</button>';
                        selectedTagsEl.appendChild(chip);
                    });
                    hiddenTags.value = tagList.join(',');
                }

                function addTag(value) {
                    var tag = (value || '').trim().toLowerCase();
                    if (!tag || tagList.indexOf(tag) !== -1) return;
                    tagList.push(tag);
                    renderTags();
                }

                var initial = (hiddenTags.value || '').split(',').map(function(v){ return v.trim().toLowerCase(); }).filter(Boolean);
                tagList = Array.from(new Set(initial));
                tagBox.style.display = 'none';
                toggleBtn.innerHTML = '<i class="bi bi-tags"></i> Show Tag Search';
                renderTags();

                toggleBtn.addEventListener('click', function() {
                    var isHidden = tagBox.style.display === 'none' || tagBox.style.display === '';
                    tagBox.style.display = isHidden ? 'block' : 'none';
                    toggleBtn.innerHTML = isHidden
                        ? '<i class="bi bi-tags"></i> Hide Tag Search'
                        : '<i class="bi bi-tags"></i> Show Tag Search';
                });

                tagInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addTag(tagInput.value);
                        tagInput.value = '';
                    }
                });

                selectedTagsEl.addEventListener('click', function(e) {
                    if (e.target.tagName.toLowerCase() === 'button') {
                        var idx = parseInt(e.target.getAttribute('data-idx'), 10);
                        if (!isNaN(idx)) {
                            tagList.splice(idx, 1);
                            renderTags();
                        }
                    }
                });

                if (suggestedTagsEl) {
                    suggestedTagsEl.addEventListener('click', function(e) {
                        if (e.target.tagName.toLowerCase() === 'button') {
                            addTag(e.target.getAttribute('data-tag'));
                        }
                    });
                }
            })();
        </script>

        <!-- Categories Section -->
        <div class="mt-5">
            <h4 class="fw-bold mb-3">Browse Collections</h4>
            <div class="collection-list">
                <a href="{{ route('mides.graduate.categories') }}" class="collection-link">
                    <div class="collection-item">
                        <h5><i class="bi bi-journal-bookmark me-2"></i>Graduate Theses</h5>
                        <div class="text-muted small">Masters and related research</div>
                    </div>
                </a>
                <a href="{{ route('mides.undergrad.programs') }}" class="collection-link">
                    <div class="collection-item">
                        <h5><i class="bi bi-journal-text me-2"></i>Undergraduate Baby Thesis</h5>
                        <div class="text-muted small">Undergraduate research</div>
                    </div>
                </a>
                <a href="{{ route('mides.faculty_theses') }}" class="collection-link">
                    <div class="collection-item">
                        <h5><i class="bi bi-person-badge me-2"></i>Faculty, Theses, and Dissertations</h5>
                        <div class="text-muted small">Faculty publications, theses, and dissertations</div>
                    </div>
                </a>
                <a href="{{ route('mides.seniorhigh.programs') }}" class="collection-link">
                    <div class="collection-item">
                        <h5><i class="bi bi-mortarboard me-2"></i>Senior High School Research Paper</h5>
                        <div class="text-muted small">ABM, HUMSS, STEM, TVL, ICT, Culinary Arts</div>
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
