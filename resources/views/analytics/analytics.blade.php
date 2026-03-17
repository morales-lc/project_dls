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
        <h3 class="fw-bold">📊 Admin Analytics — Resource Usage</h3>
    </div>

    <!-- MIDES / SIDLAK Tabs -->
    <ul class="nav nav-tabs mb-3" id="documentTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'mides' ? 'active' : '' }}"
                href="{{ route('analytics.analytics', ['type' => 'mides']) }}">
                📘 MIDES (Views)
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $documentType === 'sidlak' ? 'active' : '' }}"
                href="{{ route('analytics.analytics', ['type' => 'sidlak']) }}">
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
                            {{ $documentType }} — Program Totals
                        </h5>
                        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 shadow-sm">
                            Total Programs: {{ $programs->count() }}
                        </span>
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
                </div>
            </div>
        </div>


        <!-- Chart View -->
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
                        <canvas id="programChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
$chartLabels = $programs->pluck('name')->toArray();
$chartTotals = $programs->map(fn($p) => ($programCounts->get($p->name)?->sum('total')) ?? 0)->toArray();
@endphp

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('programChart');
        const chartLabels = @json($chartLabels);
        const chartData = @json($chartTotals);

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