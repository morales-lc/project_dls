@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link href="{{ asset('css/chart.css') }}" rel="stylesheet"><!--  chart view CSS -->
<link href="{{ asset('css/table.css') }}" rel="stylesheet"> <!--  table view CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- MIDES / SIDLAK Tabs -->
    <ul class="nav nav-tabs mb-3" id="documentTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'mides' ? 'active' : '' }}"
                href="{{ route('admin.analytics', array_merge(request()->query(), ['type' => 'mides'])) }}">
                📘 MIDES (Views)
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'sidlak' ? 'active' : '' }}"
                href="{{ route('admin.analytics', array_merge(request()->query(), ['type' => 'sidlak'])) }}">
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

        // 🎨 Light pastel colors
        const pastelColors = [
            'rgba(255, 182, 193, 1)', // Light Pink
            'rgba(173, 216, 230, 1)', // Light Blue
            'rgba(144, 238, 144, 1)', // Light Green
            'rgba(255, 255, 153, 1)', // Light Yellow
            'rgba(221, 160, 221, 1)', // Plum
            'rgba(255, 218, 185, 1)', // Peach Puff
            'rgba(176, 224, 230, 1)', // Powder Blue
            'rgba(240, 230, 140, 1)', // Khaki
            'rgba(255, 222, 173, 1)', // Navajo White
            'rgba(152, 251, 152, 1)' // Pale Green
        ];

        const backgroundColors = chartLabels.map((_, i) => pastelColors[i % pastelColors.length]);
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
                maintainAspectRatio: false, // 🔥 allows chart to fill container height
                plugins: {
                    legend: {
                        display: type !== 'bar' && type !== 'line'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                layout: {
                    padding: 10
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
            }
        });
        ctx.parentElement.classList.add('animate__animated', 'animate__fadeIn');
        // 📊 Create default chart
        let chartInstance = new Chart(ctx, config('bar'));

        // If year mode, set up monthly chart and interactions
        let monthlyChart = null;
        const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        function buildMonthlyDataset(values) {
            return {
                type: 'line',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Monthly Total',
                        data: values,
                        backgroundColor: 'rgba(54, 162, 235, 0.3)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
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
            monthlyChart = new Chart(target.getContext('2d'), buildMonthlyDataset(values));
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
                const points = chartInstance.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, false);
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

        // 🎚️ Handle chart type switching
        document.getElementById('chartTypeSelect').addEventListener('change', function() {
            const selectedType = this.value;

            // ✅ Set fixed size based on chart type (no endless resizing)
            if (['pie', 'doughnut', 'polarArea'].includes(selectedType)) {
                ctx.parentElement.style.height = '350px';
            } else {
                ctx.parentElement.style.height = '500px';
            }

            chartInstance.destroy();
            chartInstance = new Chart(ctx, config(selectedType));
        });

        // 🧠 Persist active subtab
        const subTabKey = 'activeSubTab';
        const savedSub = localStorage.getItem(subTabKey);
        if (savedSub) {
            const subTab = document.querySelector(`#${savedSub}`);
            if (subTab) new bootstrap.Tab(subTab).show();
        }
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem(subTabKey, e.target.id);
            });
        });

        // ✅ Set default container height initially
        ctx.parentElement.style.height = '500px';
    });
</script>


@endsection