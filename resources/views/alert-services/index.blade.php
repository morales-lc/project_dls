<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Services</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('navbar')

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center" style="background: #f7f8fa;">
        <div class="alert-card p-5 shadow rounded-4"
            style="background: #fff; max-width: 760px; width: 100%; margin: 2.5rem auto; border: 2.5px solid #4a90e2;">
            <h2 class="fw-bold mb-4 text-center"
                style="letter-spacing: 1px; font-size: 2.3rem; color: #d81b60;">
                Alert Services
            </h2>

            <style>
                .alert-card {
                    box-shadow: 0 6px 36px 0 rgba(216, 27, 96, 0.12);
                    border: 2.5px solid #4a90e2 !important;
                    transition: all 0.25s ease;
                }

                /* Make mobile responsive */
                @media (max-width: 600px) {
                    .alert-card {
                        max-width: 95vw;
                        padding: 1.2rem;
                    }

                    .alert-list-month {
                        font-size: 1rem;
                    }

                    .alert-link {
                        font-size: 0.98rem;
                    }
                }

                .alert-list-month {
                    font-weight: bold;
                    margin-top: 2.2rem;
                    margin-bottom: 0.7rem;
                    font-size: 1.08rem;
                    letter-spacing: 1px;
                    color: #222;
                    text-align: left;
                }

                .alert-list {
                    list-style: none;
                    padding-left: 0;
                    margin-bottom: 0.5rem;
                }

                .alert-list li {
                    margin-bottom: 0.35rem;
                    display: flex;
                    align-items: center;
                    transition: background 0.13s;
                    border-radius: 0.5em;
                    padding: 0.1em 0.2em;
                }

                .alert-list li:hover {
                    background: #f3e6ef;
                }

                .alert-bullet {
                    color: #d81b60;
                    font-size: 1.1em;
                    margin-right: 0.7em;
                    margin-left: 0.1em;
                }

                .alert-link {
                    font-weight: 600;
                    text-decoration: none;
                    font-size: 1.08rem;
                    letter-spacing: 0.5px;
                    transition: color 0.13s;
                }

                .alert-link.green {
                    color: #388e3c;
                }

                .alert-link.blue {
                    color: #1976d2;
                }

                .alert-link.pink {
                    color: #d81b60;
                }

                .alert-link.orange {
                    color: #f57c00;
                }

                .alert-link.navy {
                    color: #003366;
                }

                .alert-link.gold {
                    color: #bfa100;
                }

                .alert-link.purple {
                    color: #8e24aa;
                }

                .alert-link.maroon {
                    color: #ad1457;
                }

                .alert-link.teal {
                    color: #008080;
                }

                .alert-link.black {
                    color: #222;
                }

                .alert-link:hover {
                    text-decoration: underline;
                }

                .alert-card {
                    box-shadow: 0 4px 32px 0 rgba(216, 27, 96, 0.10);
                    border: 2.5px solid #4a90e2 !important;
                }

                @media (max-width: 600px) {
                    .alert-card {
                        max-width: 98vw;
                        padding: 1.2rem;
                    }

                    .alert-list-month {
                        font-size: 1rem;
                    }

                    .alert-link {
                        font-size: 0.98rem;
                    }
                }
            </style>

            @php
            $colorClasses = ['green', 'blue', 'pink', 'orange', 'navy', 'gold', 'purple', 'maroon', 'teal', 'black'];
            $grouped = $books->groupBy(['year', function($book) {
            return str_pad($book->month, 2, '0', STR_PAD_LEFT);
            }]);
            $years = $grouped->keys()->sort(); // ✅ Oldest to newest
            $grouped = $grouped->sortKeys(); // ✅ Sort grouped data by year to match $years order
            @endphp

            @if($books->count())
            <!-- ===== Tabs (Year Navigation) ===== -->
            <ul class="nav nav-tabs justify-content-center mb-3" id="yearTabs" role="tablist">
                @foreach($years as $index => $year)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $year }}" data-bs-toggle="tab" data-bs-target="#year-{{ $year }}" type="button" role="tab">
                        {{ $year }}
                    </button>
                </li>
                @endforeach
            </ul>

            <!-- ===== Tab Content ===== -->
            <div class="tab-content" id="yearTabsContent">
                @foreach($grouped as $year => $months)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="year-{{ $year }}" role="tabpanel">
                    @foreach($months as $month => $booksInMonth)
                    @php
                    $deptGroups = $booksInMonth->groupBy('department_id');
                    $colorIndex = 0;
                    @endphp
                    <div>
                        <div class="alert-list-month text-uppercase" style="font-size:1.05rem; color:#222; margin-top:0.7rem;">
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </div>
                        <ul class="alert-list">
                            @foreach($deptGroups as $deptId => $deptBooks)
                            @php
                            $dept = $departments->find($deptId);
                            $colorClass = $colorClasses[$colorIndex % count($colorClasses)];
                            $colorIndex++;
                            @endphp
                            @if($dept)
                            <li>
                                <span class="alert-bullet">&#9632;</span>
                                <a class="alert-link {{ $colorClass }}" href="{{ route('alert-services.group', ['year'=>$year, 'month'=>$month, 'group'=>$dept->type, 'value'=>urlencode($dept->name)]) }}">
                                    {{ strtoupper($dept->name) }}
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            @else
            <div class="alert alert-warning mt-4 text-center">No books found.</div>
            @endif
        </div>
    </div>

    @include('footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>