@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('title', 'User Login Analytics')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold text-pink mb-0">User Login Analytics</h2>
            <small class="text-muted">Timeframe: {{ $timeLabel }}</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.login.analytics.export', request()->query()) }}" class="btn btn-outline-secondary">
                <i class="bi bi-download me-2"></i>Export CSV
            </a>
            <a href="{{ route('admin.login.analytics.export.xlsx', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('admin.analytics') }}" class="btn btn-outline-pink">
                <i class="bi bi-bar-chart me-2"></i>Resource Analytics
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Timeframe</label>
                    <select name="mode" id="modeSelect" class="form-select form-select-sm">
                        <option value="year" {{ $mode === 'year' ? 'selected' : '' }}>Year</option>
                        <option value="month" {{ $mode === 'month' ? 'selected' : '' }}>Month and Year</option>
                        <option value="semester" {{ $mode === 'semester' ? 'selected' : '' }}>Custom Date Range</option>
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
                <div class="col-6 col-md-2 mode-month">
                    <label class="form-label mb-1">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)$month === $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2 mode-semester">
                    <label class="form-label mb-1">Start date</label>
                    <input type="date" name="start_date" value="{{ $startDateInput }}" class="form-control form-control-sm" />
                </div>
                <div class="col-6 col-md-2 mode-semester">
                    <label class="form-label mb-1">End date</label>
                    <input type="date" name="end_date" value="{{ $endDateInput }}" class="form-control form-control-sm" />
                </div>
                <div class="col-12 col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Rows</label>
                    <select name="per_page" class="form-select form-select-sm">
                        @foreach([10, 20, 30, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int)($perPage ?? 20) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-blue solid">
                <div class="card-body">
                    <div class="text-uppercase small">Total Login Events</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['total_logins'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-amber solid">
                <div class="card-body">
                    <div class="text-uppercase small">Unique Users Logged In</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['unique_users'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-teal solid">
                <div class="card-body">
                    <div class="text-uppercase small">Unique Students</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['unique_students'] }}</div>
                    <small class="text-muted">Logins: {{ $summary['student_logins'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 stat-card is-indigo solid">
                <div class="card-body">
                    <div class="text-uppercase small">Unique Faculty</div>
                    <div class="h2 fw-bold mb-0 mt-2">{{ $summary['unique_faculty'] }}</div>
                    <small class="text-muted">Logins: {{ $summary['faculty_logins'] }}</small>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills mb-3" id="analyticsSubTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-view" type="button" role="tab">Table View</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart-view" type="button" role="tab">Chart Summary</button>
        </li>
    </ul>

    <div class="tab-content" id="analyticsSubTabContent">
        <div class="tab-pane fade show active" id="table-view" role="tabpanel" aria-labelledby="table-tab">
            <div class="row g-4">
                <div class="col-12 col-xl-7">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="mb-3 text-pink">Student Logins by Program and Course</h5>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Program</th>
                                            <th>Course</th>
                                            <th class="text-center">Unique Students</th>
                                            <th class="text-center">Login Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($studentByProgramCourse as $row)
                                            <tr>
                                                <td>{{ $row->program_name }}</td>
                                                <td>{{ $row->course_name }}</td>
                                                <td class="text-center">{{ $row->unique_users }}</td>
                                                <td class="text-center">{{ $row->total_logins }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No student login data found for the selected timeframe.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $studentByProgramCourse->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="mb-3 text-pink">Faculty Logins by Program</h5>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Program</th>
                                            <th class="text-center">Unique Faculty</th>
                                            <th class="text-center">Login Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($facultyByProgram as $row)
                                            <tr>
                                                <td>{{ $row->program_name }}</td>
                                                <td class="text-center">{{ $row->unique_users }}</td>
                                                <td class="text-center">{{ $row->total_logins }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No faculty login data found for the selected timeframe.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $facultyByProgram->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-body">
                    <h5 class="mb-3 text-pink">Recent Login Logs</h5>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead>
                                <tr>
                                    <th>Date and Time</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Program</th>
                                    <th>Course</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                    @php
                                        $displayName = trim(($log->studentFaculty->first_name ?? '') . ' ' . ($log->studentFaculty->last_name ?? ''));
                                        $displayName = $displayName !== '' ? $displayName : ($log->user->name ?? 'Unknown');
                                    @endphp
                                    <tr>
                                        <td>{{ optional($log->logged_in_at)->format('M d, Y h:i A') }}</td>
                                        <td>{{ $displayName }}</td>
                                        <td class="text-capitalize">{{ $log->role }}</td>
                                        <td>{{ $log->studentFaculty->program->name ?? 'Unknown' }}</td>
                                        <td>{{ $log->course ?: 'Unassigned' }}</td>
                                        <td>{{ $log->ip_address ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No logs found for the selected timeframe.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $recentLogs->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="chart-view" role="tabpanel" aria-labelledby="chart-tab">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0 text-pink">Summary Charts by Program and Course</h5>
                    <div class="d-flex align-items-center gap-2">
                        <label for="chartTypeSelect" class="mb-0 small text-muted">Chart Type</label>
                        <select id="chartTypeSelect" class="form-select form-select-sm" style="width: 160px;">
                            <option value="bar" selected>Bar</option>
                            <option value="pie">Pie</option>
                            <option value="line">Line</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="text-pink mb-3">Login Events by Program</h6>
                            <div style="height: 380px;">
                                <canvas id="programSummaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="text-pink mb-3">Student Login Events by Course</h6>
                            <div style="height: 380px;">
                                <canvas id="courseSummaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modeSelect = document.getElementById('modeSelect');

        function updateModeFields() {
            const mode = modeSelect.value;
            document.querySelectorAll('.mode-year').forEach(el => el.style.display = (mode === 'year') ? '' : 'none');
            document.querySelectorAll('.mode-month').forEach(el => el.style.display = (mode === 'month') ? '' : 'none');
            document.querySelectorAll('.mode-semester').forEach(el => el.style.display = (mode === 'semester') ? '' : 'none');
        }

        updateModeFields();
        modeSelect.addEventListener('change', updateModeFields);

        const programLabels = @json($programChartLabels ?? []);
        const programValues = @json($programChartValues ?? []);
        const courseLabels = @json($courseChartLabels ?? []);
        const courseValues = @json($courseChartValues ?? []);

        const baseColors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(99, 102, 241, 0.8)',
            'rgba(14, 165, 233, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(132, 204, 22, 0.8)',
        ];

        function getColors(length) {
            const bg = [];
            const border = [];
            for (let i = 0; i < length; i++) {
                const c = baseColors[i % baseColors.length];
                bg.push(c);
                border.push(c.replace('0.8', '1'));
            }
            return { bg, border };
        }

        let programChart = null;
        let courseChart = null;

        function buildConfig(type, labels, values, label) {
            const colors = getColors(labels.length);
            return {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: values,
                        backgroundColor: colors.bg,
                        borderColor: colors.border,
                        borderWidth: 2,
                        tension: 0.25,
                        fill: type === 'line',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                    scales: (type === 'pie') ? {} : {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            };
        }

        function renderCharts(type) {
            const programCanvas = document.getElementById('programSummaryChart');
            const courseCanvas = document.getElementById('courseSummaryChart');
            if (!programCanvas || !courseCanvas) return;

            if (programChart) programChart.destroy();
            if (courseChart) courseChart.destroy();

            programChart = new Chart(
                programCanvas.getContext('2d'),
                buildConfig(type, programLabels, programValues, 'Program Logins')
            );

            courseChart = new Chart(
                courseCanvas.getContext('2d'),
                buildConfig(type, courseLabels, courseValues, 'Course Logins')
            );
        }

        const chartTypeSelect = document.getElementById('chartTypeSelect');
        if (chartTypeSelect) {
            renderCharts(chartTypeSelect.value);
            chartTypeSelect.addEventListener('change', function () {
                renderCharts(this.value);
            });
        }
    });
</script>
@endsection
