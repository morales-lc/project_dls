@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link href="{{ asset('css/chart.css') }}" rel="stylesheet">
<link href="{{ asset('css/table.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('title', 'Admin Analytics')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">📊 Admin Analytics — Resource Usage</h3>
    </div>

    <div class="mb-3 d-flex align-items-center gap-3">
        <form method="GET" class="d-flex gap-2 align-items-center" id="rangeForm">
            <input type="hidden" name="type" value="{{ $documentType }}">
            <label class="mb-0">Range:</label>
            <select name="range" id="rangeSelect" class="form-select form-select-sm">
                <option value="year" {{ ($range ?? 'year') === 'year' ? 'selected' : '' }}>Year</option>
                <option value="month" {{ ($range ?? '') === 'month' ? 'selected' : '' }}>Month</option>
                <option value="sem" {{ ($range ?? '') === 'sem' ? 'selected' : '' }}>Semester (custom)</option>
            </select>

            <div id="yearControl" class="d-flex gap-2 align-items-center">
                <label class="mb-0">Year:</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (int)($year ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div id="monthControl" class="d-flex gap-2 align-items-center" style="display:none;">
                <label class="mb-0">Month:</label>
                <select name="month" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ((int)($month ?? 0)) === $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                    @endfor
                </select>
            </div>

            <div id="semControl" class="d-flex gap-2 align-items-center" style="display:none;">
                <label class="mb-0">Start:</label>
                <input type="date" name="sem_start" class="form-control form-control-sm" value="{{ $semStart ?? '' }}">
                <label class="mb-0">End:</label>
                <input type="date" name="sem_end" class="form-control form-control-sm" value="{{ $semEnd ?? '' }}">
            </div>

            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
        </form>
    </div>

    <!-- MIDES / SIDLAK Tabs -->
    <ul class="nav nav-tabs mb-3" id="documentTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'mides' ? 'active' : '' }}"
                href="{{ route('admin.analytics', ['type' => 'mides']) }}">
                📘 MIDES (Views)
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'sidlak' ? 'active' : '' }}"
                href="{{ route('admin.analytics', ['type' => 'sidlak']) }}">
                📗 SIDLAK (Downloads)
            </a>
        </li>
    </ul>

    <!-- Compute chart data BEFORE tabs -->
    @php
        if (!isset($programCounts)) $programCounts = collect();
        $chartLabels = $programs->pluck('name')->toArray();
        $chartTotals = $programs->map(function($p) use ($programCounts) {
            $grp = $programCounts->get($p->name) ?? collect();
            return (int) $grp->sum('total');
        })->toArray();
    @endphp

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

        <!-- TABLE VIEW -->
        <div class="tab-pane fade show active" id="table-view" role="tabpanel">
            @include('admin.analytics.table-view')
        </div>

        <!-- CHART VIEW -->
        <div class="tab-pane fade" id="chart-view" role="tabpanel">
            <div class="card shadow-sm border-0 chart-card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h5 class="fw-semibold text-primary text-uppercase mb-0">
                            {{ $documentType }} — Program Usage Chart
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
                        <canvas id="programChart"
                            data-labels='@json($chartLabels)'
                            data-data='@json($chartTotals)'></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================== JS SECTION ================== --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('programChart');
    const chartLabels = JSON.parse(ctx.dataset.labels || '[]');
    const chartData = JSON.parse(ctx.dataset.data || '[]');

    const pastelColors = [
        'rgba(255, 182, 193, 1)',
        'rgba(173, 216, 230, 1)',
        'rgba(144, 238, 144, 1)',
        'rgba(255, 255, 153, 1)',
        'rgba(221, 160, 221, 1)',
        'rgba(255, 218, 185, 1)',
        'rgba(176, 224, 230, 1)',
        'rgba(240, 230, 140, 1)',
        'rgba(255, 222, 173, 1)',
        'rgba(152, 251, 152, 1)'
    ];

    const chartConfig = (type, labels, data) => ({
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: '{{ strtoupper($documentType) }} Totals',
                data: data,
                backgroundColor: labels.map((_, i) => pastelColors[i % pastelColors.length]),
                borderWidth: 1.5,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: type !== 'bar' && type !== 'line' }
            },
            scales: (type === 'bar' || type === 'line') ? {
                x: { beginAtZero: true, ticks: { autoSkip: false, maxRotation: 45 } },
                y: { beginAtZero: true }
            } : {}
        }
    });

    let chartInstance = new Chart(ctx, chartConfig('bar', chartLabels, chartData));
    ctx.parentElement.style.height = '500px';

    // 🔄 Chart type switch
    document.getElementById('chartTypeSelect').addEventListener('change', function() {
        const type = this.value;
        if (chartInstance) chartInstance.destroy();
        ctx.parentElement.style.height = ['pie','doughnut','polarArea'].includes(type) ? '350px' : '500px';
        chartInstance = new Chart(ctx, chartConfig(type, chartLabels, chartData));
    });

    // 🔁 AJAX Refresh (optional enhancement)
    document.getElementById('rangeForm').addEventListener('change', function() {
        const params = new URLSearchParams(new FormData(this)).toString();
        fetch(`{{ route('admin.analytics.chart-data') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                if (chartInstance) chartInstance.destroy();
                chartInstance = new Chart(ctx, chartConfig('bar', data.labels, data.data));
            });
    });
});
</script>

@endsection
