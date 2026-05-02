@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link href="{{ asset('css/chart.css') }}" rel="stylesheet"><!--  chart view CSS -->
<link href="{{ asset('css/table.css') }}" rel="stylesheet"> <!--  table view CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
@endpush

@section('title', 'Admin Analytics')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold"> Analytics — Resource Usage</h3>
    </div>

    <div class="mb-3 d-flex align-items-center gap-3">
        <form method="GET" class="w-100">
            <input type="hidden" name="type" value="{{ $documentType }}">
            <input type="hidden" name="active_sub_tab" id="activeSubTabInput" value="{{ request('active_sub_tab', 'table-tab') }}">
            <input type="hidden" name="top_mides_limit" value="{{ (int) ($topMidesLimit ?? 10) }}">
            @if(request('action'))
            <input type="hidden" name="action" value="{{ request('action') }}">
            @endif

            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Timeframe</label>
                    <select name="mode" id="modeSelect" class="form-select form-select-sm">
                        <option value="year" {{ $mode === 'year' ? 'selected' : '' }}>Year(Current Year)</option>
                        <option value="month" {{ $mode === 'month' ? 'selected' : '' }}>Month & Year</option>
                        <option value="semester" {{ $mode === 'semester' ? 'selected' : '' }}>Semester </option>
                    </select>
                </div>
                <div class="col-6 col-md-2 mode-year mode-month">
                    <label class="form-label mb-1">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2 mode-month" id="monthWrap">
                    <label class="form-label mb-1">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)($month ?? date('n')) === $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                            @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2 mode-semester" id="startWrap">
                    <label class="form-label mb-1">Start date</label>
                    <input type="date" name="start_date" value="{{ $startDateInput }}" class="form-control form-control-sm" />
                </div>
                <div class="col-6 col-md-2 mode-semester" id="endWrap">
                    <label class="form-label mb-1">End date</label>
                    <input type="date" name="end_date" value="{{ $endDateInput }}" class="form-control form-control-sm" />
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Most searched term ({{ strtoupper($documentType) }}, {{ $timeLabel }})</div>
                    @if(!empty($mostSearchedTerm))
                        <div class="fs-5 fw-bold text-primary">{{ $mostSearchedTerm }}</div>
                        <div class="small text-muted">Search hits: {{ $mostSearchedCount }}</div>
                    @else
                        <div class="fs-6 text-muted">No tracked search terms in this timeframe yet.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        {{ $documentType === 'mides' ? 'Most visited MIDES category' : 'Most visited SIDLAK journal' }} ({{ $timeLabel }})
                    </div>
                    @if($documentType === 'mides' && !empty($mostVisitedMidesCategory))
                        <div class="fs-5 fw-bold text-success">{{ $mostVisitedMidesCategory }}</div>
                        <div class="small text-muted">Total views: {{ $mostVisitedMidesCategoryCount }}</div>
                    @elseif($documentType === 'sidlak' && !empty($mostVisitedSidlakJournal))
                        <div class="fs-5 fw-bold text-success">{{ $mostVisitedSidlakJournal }}</div>
                        <div class="small text-muted">Total downloads: {{ $mostVisitedSidlakJournalCount }}</div>
                    @else
                        <div class="fs-6 text-muted">No data in this timeframe yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MIDES / SIDLAK Tabs -->
    <ul class="nav nav-tabs mb-3" id="documentTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'mides' ? 'active' : '' }}"
                href="{{ route('admin.analytics', array_merge(request()->except(['type', 'active_sub_tab']), ['type' => 'mides'])) }}">
                📘 MIDES (Views)
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'sidlak' ? 'active' : '' }}"
                href="{{ route('admin.analytics', array_merge(request()->except(['type', 'active_sub_tab']), ['type' => 'sidlak'])) }}">
                📗 SIDLAK (Downloads)
            </a>
        </li>
    </ul>

    <!-- Sub Tabs -->
    <ul class="nav nav-pills mb-3" id="subTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-view" type="button" role="tab">
                📑 Table View
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart-view" type="button" role="tab">
                📈 Chart View
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="most-searched-tab" data-bs-toggle="tab" data-bs-target="#most-searched-view" type="button" role="tab">
                🔎 Most Searched Terms
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="most-visited-mides-tab" data-bs-toggle="tab" data-bs-target="#most-visited-mides-view" type="button" role="tab">
                {{ $documentType === 'mides' ? '🗂️ Most Visited MIDES Programs' : '📰 Most Visited SIDLAK Journal' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="top-mides-tab" data-bs-toggle="tab" data-bs-target="#top-mides-view" type="button" role="tab">
                {{ $documentType === 'mides' ? '🏆 Top MIDES Documents' : '🏆 Top SIDLAK Articles' }}
            </button>
        </li>
    </ul>

    <div class="tab-content" id="subTabContent">
        <!-- Table View -->
        <div class="tab-pane fade show active" id="table-view" role="tabpanel">
            <div class="card mb-4 border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold text-primary text-uppercase mb-0">
                            {{ $documentType }} — Program Totals ({{ $timeLabel }})
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 shadow-sm">
                                Total Programs: {{ $programs->count() }}
                            </span>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.analytics.export', request()->query()) }}">
                                <i class="bi bi-download me-1"></i> Export CSV
                            </a>
                            <a class="btn btn-success btn-sm" href="{{ route('admin.analytics.export.xlsx', request()->query()) }}">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover analytics-table">
                            <thead>
                                <tr>
                                    <th scope="col">Program</th>
                                    <th scope="col" class="text-center">Total</th>
                                    <th scope="col" class="text-center">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programs as $program)
                                @php
                                $group = $programCounts->get($program->name) ?? collect();
                                $total = $group->sum('total');
                                $allCourses = $program->courses;
                                $courses = $courseCounts->get($program->id) ?? collect();
                                $coursesByName = $courses->groupBy('course');
                                @endphp

                                <tr class="table-row">
                                    <td class="fw-semibold">{{ $program->name }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $total }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm toggle-details" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $loop->index }}" aria-expanded="false">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr class="collapse bg-light" id="collapse-{{ $loop->index }}">
                                    <td colspan="3" class="p-3">
                                        <div class="p-3 rounded-3 bg-white border">
                                            <h6 class="fw-semibold text-secondary mb-3">
                                                <i class="bi bi-diagram-3 me-1 text-primary"></i> Course Breakdown
                                            </h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Course</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($allCourses as $course)
                                                    @php
                                                    $rows = $coursesByName->get($course->name) ?? collect();
                                                    $courseTotal = $rows->sum('total');
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $course->name }}</td>
                                                        <td class="text-center">{{ $courseTotal }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($mode === 'year')
                    <!-- Monthly breakdown -->
                    <div class="mt-4">
                        <h6 class="fw-semibold">Monthly breakdown ({{ $year }})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        @for($m = 1; $m <= 12; $m++)
                                            <th class="text-center">{{ DateTime::createFromFormat('!m', $m)->format('M') }}</th>
                                            @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @for($m = 1; $m <= 12; $m++)
                                            <td class="text-center fw-semibold">{{ $monthlyCounts[$m] ?? 0 }}</td>
                                            @endfor
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Semester totals -->
                    <div class="mt-3">
                        <h6 class="fw-semibold">Semester totals</h6>
                        <div class="d-flex gap-3 mt-2">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted">Jan – Jun</div>
                                <div class="fs-5 fw-bold">{{ $semester1 }}</div>
                            </div>
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted">Jul – Dec</div>
                                <div class="fs-5 fw-bold">{{ $semester2 }}</div>
                            </div>
                        </div>
                    </div>
                    @endif



                </div>
            </div>
        </div>


        <!-- Chart View -->
        <div class="tab-pane fade" id="chart-view" role="tabpanel">
            <div class="card shadow-sm border-0 chart-card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h5 class="fw-semibold text-primary text-uppercase mb-0">
                            {{ $documentType }} — Program Usage Chart ({{ $timeLabel }})
                        </h5>
                        <div class="d-flex align-items-center mt-2 mt-sm-0">
                            <label for="chartTypeSelect" class="me-2 fw-semibold text-secondary">
                                Chart Type:
                            </label>
                            <select id="chartTypeSelect" class="form-select form-select-sm border-primary-subtle shadow-sm w-auto">
                                <option value="bar" selected>Bar</option>
                                <option value="line">Line</option>
                                <option value="pie">Pie</option>
                                <option value="doughnut">Doughnut</option>
                                <option value="polarArea">Polar Area</option>
                                <option value="radar">Radar</option>
                            </select>
                        </div>
                    </div>

                    <div class="chart-container position-relative p-3 rounded-4 bg-light">
                        <canvas id="programChart"></canvas>
                    </div>

                    @if($mode === 'year')
                    <hr class="my-4" />
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-semibold">Monthly trend by program ({{ $year }})</h6>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 small text-muted" for="programSelect">Program:</label>
                            <select id="programSelect" class="form-select form-select-sm">
                                @foreach($programs as $p)
                                <option value="{{ $p->name }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="chart-container position-relative p-3 rounded-4 bg-light" style="height: 380px;">
                        <canvas id="programMonthlyChart"></canvas>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="most-searched-view" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="fw-semibold text-primary text-uppercase mb-0">Most Searched Terms ({{ $timeLabel }})</h5>
                        <div class="d-flex align-items-center gap-2">
                            <label for="mostSearchedTypeSelector" class="small text-muted mb-0">Collection</label>
                            <select id="mostSearchedTypeSelector" class="form-select form-select-sm" style="width:auto;">
                                <option value="mides" {{ $documentType === 'mides' ? 'selected' : '' }}>MIDES</option>
                                <option value="sidlak" {{ $documentType === 'sidlak' ? 'selected' : '' }}>SIDLAK</option>
                            </select>
                        </div>
                    </div>

                    <div id="mostSearchedTopCard" class="p-3 rounded-3 bg-light border mb-3"></div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Rank</th>
                                    <th>Search Term</th>
                                    <th class="text-center" style="width:160px;">Hits</th>
                                </tr>
                            </thead>
                            <tbody id="mostSearchedTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="most-visited-mides-view" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold text-primary text-uppercase mb-3">
                        {{ $documentType === 'mides' ? 'Most Visited MIDES Categories' : 'Most Visited SIDLAK Journals' }} ({{ $timeLabel }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Rank</th>
                                    <th>{{ $documentType === 'mides' ? 'Category' : 'Journal' }}</th>
                                    @if($documentType === 'sidlak')
                                        <th style="width:170px;">Issue</th>
                                    @endif
                                    <th class="text-center" style="width:160px;">{{ $documentType === 'mides' ? 'Views' : 'Downloads' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($documentType === 'mides')
                                    @forelse($mostVisitedMidesCategories as $idx => $row)
                                        <tr>
                                            <td>#{{ $idx + 1 }}</td>
                                            <td>{{ $row['category_name'] }}</td>
                                            <td class="text-center fw-semibold">{{ $row['total'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">No MIDES category views in this timeframe yet.</td>
                                        </tr>
                                    @endforelse
                                @else
                                    @forelse($mostVisitedSidlakJournals as $idx => $row)
                                        <tr>
                                            <td>#{{ $idx + 1 }}</td>
                                            <td>
                                                <a href="{{ route('sidlak.show', $row['journal_id']) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                    {{ $row['journal_title'] }}
                                                </a>
                                            </td>
                                            <td>{{ $row['journal_period'] ?: '—' }}</td>
                                            <td class="text-center fw-semibold">{{ $row['total'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">No SIDLAK journal downloads in this timeframe yet.</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="top-mides-view" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="fw-semibold text-primary text-uppercase mb-0">
                            {{ $documentType === 'mides' ? 'Top MIDES Documents by Views' : 'Top SIDLAK Articles by Downloads' }} ({{ $timeLabel }})
                        </h5>
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="type" value="{{ $documentType }}">
                            <input type="hidden" name="mode" value="{{ $mode }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="start_date" value="{{ $startDateInput }}">
                            <input type="hidden" name="end_date" value="{{ $endDateInput }}">
                            <input type="hidden" name="active_sub_tab" value="top-mides-tab">
                            @if(request('action'))
                                <input type="hidden" name="action" value="{{ request('action') }}">
                            @endif

                            <label for="topMidesLimit" class="small text-muted mb-0">Show top</label>
                            <select id="topMidesLimit" name="top_mides_limit" class="form-select form-select-sm" style="width:auto;">
                                <option value="10" {{ (int) $topMidesLimit === 10 ? 'selected' : '' }}>10</option>
                                <option value="50" {{ (int) $topMidesLimit === 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ (int) $topMidesLimit === 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                        </form>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-hover align-middle">
                            @if($documentType === 'mides')
                                <thead>
                                    <tr>
                                        <th style="width:90px;">Rank</th>
                                        <th>Document Title</th>
                                        <th style="width:210px;">Author</th>
                                        <th style="width:220px;">Collection Type</th>
                                        <th class="text-center" style="width:120px;">Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topMidesDocuments as $idx => $row)
                                        <tr>
                                            <td>#{{ $idx + 1 }}</td>
                                            <td>
                                                <a href="{{ route('mides.pdf.stream', $row->document_id) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                    {{ $row->title }}
                                                </a>
                                            </td>
                                            <td>{{ $row->author ?: '—' }}</td>
                                            <td>{{ $row->type ?: '—' }}</td>
                                            <td class="text-center fw-semibold">{{ (int) $row->total_views }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">No MIDES views in this timeframe yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            @else
                                <thead>
                                    <tr>
                                        <th style="width:90px;">Rank</th>
                                        <th>Article Title</th>
                                        <th style="width:210px;">Authors</th>
                                        <th style="width:240px;">Journal</th>
                                        <th class="text-center" style="width:120px;">Downloads</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSidlakArticles as $idx => $row)
                                        <tr>
                                            <td>#{{ $idx + 1 }}</td>
                                            <td>
                                                <a href="{{ route('sidlak.article.download', $row->article_id) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                    {{ $row->article_title }}
                                                </a>
                                            </td>
                                            <td>{{ $row->article_authors ?: '—' }}</td>
                                            <td>
                                                <a href="{{ route('sidlak.show', $row->journal_id) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                    {{ $row->journal_title }}
                                                </a>
                                            </td>
                                            <td class="text-center fw-semibold">{{ (int) $row->total_downloads }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">No SIDLAK downloads in this timeframe yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            @endif
                        </table>
                    </div>

                    <h6 class="fw-semibold mb-2">
                        {{ $documentType === 'mides' ? 'Bar Graph: Top 10 MIDES Documents' : 'Bar Graph: Top 10 SIDLAK Articles' }}
                    </h6>
                    <div class="chart-container position-relative p-3 rounded-4 bg-light" style="height: 430px;">
                        <canvas id="topMidesTop10Chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    @php
    $chartLabels = $programs->pluck('name')->toArray();
    $chartTotals = [];
    foreach ($programs as $p) {
    $group = isset($programCounts) ? $programCounts->get($p->name) : collect();
    $chartTotals[] = $group ? $group->sum('total') : 0;
    }
    @endphp

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle timeframe fields
        const modeSelect = document.getElementById('modeSelect');

        function updateModeFields() {
            const mode = modeSelect.value;
            document.querySelectorAll('.mode-year').forEach(el => el.style.display = (mode === 'year') ? '' : 'none');
            document.querySelectorAll('.mode-month').forEach(el => el.style.display = (mode === 'month') ? '' : 'none');
            document.querySelectorAll('.mode-semester').forEach(el => el.style.display = (mode === 'semester') ? '' : 'none');
        }
        updateModeFields();
        modeSelect.addEventListener('change', updateModeFields);

        const ctx = document.getElementById('programChart');
        const chartLabels = <?php echo json_encode($chartLabels); ?>;
        const chartData = <?php echo json_encode($chartTotals); ?>;
        const isYearMode = <?php echo json_encode($mode === 'year'); ?>;
        const monthlyByProgram = <?php echo json_encode($monthlyByProgram ?? []); ?>;
        const documentType = @json($documentType);
        const mostSearchedTermsByType = <?php echo json_encode($mostSearchedTermsByType ?? []); ?>;
        const topMidesTop10Labels = <?php echo json_encode($topMidesTop10Labels ?? []); ?>;
        const topMidesTop10Values = <?php echo json_encode($topMidesTop10Values ?? []); ?>;
        const topSidlakTop10Labels = <?php echo json_encode($topSidlakTop10Labels ?? []); ?>;
        const topSidlakTop10Values = <?php echo json_encode($topSidlakTop10Values ?? []); ?>;
        const activeSubTabInput = document.getElementById('activeSubTabInput');

        // 🌈 Rainbow basic colors
        const rainbowColors = [
            'rgba(255, 0, 0, 0.8)', // Red
            'rgba(255, 127, 0, 0.8)', // Orange
            'rgba(255, 255, 0, 0.8)', // Yellow
            'rgba(0, 255, 0, 0.8)', // Green
            'rgba(0, 0, 255, 0.8)', // Blue
            'rgba(75, 0, 130, 0.8)', // Indigo
            'rgba(148, 0, 211, 0.8)' // Violet
        ];
        const backgroundColors = chartLabels.map((_, i) => rainbowColors[i % rainbowColors.length]);
        const borderColors = backgroundColors.map(c => c.replace('0.8', '1'));

        // ⚙️ Chart configuration function
        const config = (type) => ({
            type: type,
            data: {
                labels: chartLabels,
                datasets: [{
                    label: '{{ strtoupper($documentType) }} Totals',
                    data: chartData,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1.5,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    // ✅ Add data labels plugin for pie/doughnut
                    datalabels: {
                        display: type === 'pie' || type === 'doughnut',
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 13
                        },
                        formatter: (value, ctx) => {
                            const total = ctx.chart._metasets[0].total || ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1) + '%';
                            return percentage;
                        }
                    }
                },
                layout: {
                    padding: 20
                },
                elements: {
                    arc: {
                        borderWidth: 1.5
                    }
                },
                scales: (type === 'bar' || type === 'line') ? {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                } : {}
            },
            plugins: [ChartDataLabels] // 👈 important to activate labels
        });
        ctx.parentElement.classList.add('animate__animated', 'animate__fadeIn');
        // 📊 Create default chart
        let chartInstance = new Chart(ctx, config('bar'));

        // If year mode, set up monthly chart and interactions
        let monthlyChart = null;
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        function buildMonthlyDataset(values, programName) {
            // Find the program index to use the same color
            const programIndex = chartLabels.indexOf(programName);
            const programColor = programIndex >= 0 ? backgroundColors[programIndex] : 'rgba(54, 162, 235, 0.8)';
            const programBorderColor = programIndex >= 0 ? borderColors[programIndex] : 'rgba(54, 162, 235, 1)';
            
            return {
                type: 'line',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Monthly Total',
                        data: values,
                        backgroundColor: programColor,
                        borderColor: programBorderColor,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
        }

        function updateMonthlyChart(programName) {
            if (!isYearMode) return;
            const valuesObj = monthlyByProgram[programName] || {};
            // Ensure values array of length 12
            const values = [];
            for (let m = 1; m <= 12; m++) values.push(valuesObj[m] || 0);
            const target = document.getElementById('programMonthlyChart');
            if (!target) return;
            if (monthlyChart) monthlyChart.destroy();
            monthlyChart = new Chart(target.getContext('2d'), buildMonthlyDataset(values, programName));
        }

        if (isYearMode) {
            const programSelect = document.getElementById('programSelect');
            if (programSelect) {
                // default to top program by total if available
                const defaultProgram = chartLabels[0] || programSelect.value;
                programSelect.value = defaultProgram;
                updateMonthlyChart(defaultProgram);
                programSelect.addEventListener('change', (e) => updateMonthlyChart(e.target.value));
            }

            // Click on main chart bar to drilldown
            ctx.onclick = function(evt) {
                const points = chartInstance.getElementsAtEventForMode(evt, 'nearest', {
                    intersect: true
                }, false);
                if (points.length > 0) {
                    const firstPoint = points[0];
                    const idx = firstPoint.index;
                    const programName = chartLabels[idx];
                    const programSelectEl = document.getElementById('programSelect');
                    if (programSelectEl) programSelectEl.value = programName;
                    updateMonthlyChart(programName);
                }
            };
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderMostSearched(type) {
            const tableBody = document.getElementById('mostSearchedTableBody');
            const topCard = document.getElementById('mostSearchedTopCard');
            if (!tableBody || !topCard) return;

            const rows = Array.isArray(mostSearchedTermsByType[type]) ? mostSearchedTermsByType[type] : [];
            if (!rows.length) {
                topCard.innerHTML = '<div class="text-muted">No tracked search terms for this collection and timeframe.</div>';
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No data available.</td></tr>';
                return;
            }

            topCard.innerHTML = '<div class="small text-muted">Top term</div>' +
                '<div class="fs-5 fw-bold text-primary">' + escapeHtml(rows[0].term) + '</div>' +
                '<div class="small text-muted">Hits: ' + Number(rows[0].total || 0) + '</div>';

            tableBody.innerHTML = rows.map(function(row, idx) {
                return '<tr>' +
                    '<td>#' + (idx + 1) + '</td>' +
                    '<td>' + escapeHtml(row.term) + '</td>' +
                    '<td class="text-center fw-semibold">' + Number(row.total || 0) + '</td>' +
                    '</tr>';
            }).join('');
        }

        const mostSearchedTypeSelector = document.getElementById('mostSearchedTypeSelector');
        if (mostSearchedTypeSelector) {
            renderMostSearched(mostSearchedTypeSelector.value || 'mides');
            mostSearchedTypeSelector.addEventListener('change', function() {
                renderMostSearched(this.value || 'mides');
            });
        }

        const topMidesCanvas = document.getElementById('topMidesTop10Chart');
        if (topMidesCanvas) {
            const topContentLabels = documentType === 'sidlak' ? topSidlakTop10Labels : topMidesTop10Labels;
            const topContentValues = documentType === 'sidlak' ? topSidlakTop10Values : topMidesTop10Values;
            const topContentLabel = documentType === 'sidlak' ? 'SIDLAK Downloads' : 'MIDES Views';
            new Chart(topMidesCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: topContentLabels.map(label => {
                        const text = String(label || '');
                        return text.length > 48 ? (text.slice(0, 48) + '...') : text;
                    }),
                    datasets: [{
                        label: topContentLabel,
                        data: topContentValues,
                        backgroundColor: 'rgba(13, 110, 253, 0.65)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 10,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
            });
        }

        // 🎚️ Handle chart type switching
        document.getElementById('chartTypeSelect').addEventListener('change', function() {
            const selectedType = this.value;
            //size of pie, doughnut
            if (selectedType === 'pie' || selectedType === 'doughnut') {
                ctx.parentElement.style.height = '600px'; // Bigger size for pie & doughnut
            } else if (selectedType === 'polarArea') {
                ctx.parentElement.style.height = '450px';
            } else {
                ctx.parentElement.style.height = '500px';
            }

            chartInstance.destroy();
            chartInstance = new Chart(ctx, config(selectedType));
        });

        // 🧠 Persist active subtab
        const subTabKey = 'activeSubTab_' + documentType;
        const requestedSub = new URLSearchParams(window.location.search).get('active_sub_tab');
        const savedSub = localStorage.getItem(subTabKey);
        const tabToShow = requestedSub || savedSub;
        if (tabToShow) {
            const subTab = document.querySelector(`#${tabToShow}`);
            if (subTab) new bootstrap.Tab(subTab).show();
        }
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem(subTabKey, e.target.id);
                if (activeSubTabInput) activeSubTabInput.value = e.target.id;
            });
        });

        // ✅ Set default container height initially
        ctx.parentElement.style.height = '500px';
    });
</script>


@endsection