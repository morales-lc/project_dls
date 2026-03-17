<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Library</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        /* === Header Banner Style (same as graduate.blade.php) === */
        .header-banner {
            position: relative;
            width: 100%;
            height: 300px;
            background: url('{{ asset("images/college_library.jpg") }}') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .header-banner::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
        }

        .header-text {
            position: relative;
            z-index: 2;
            font-size: 2.2rem;
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        /* === Staff Card Styles === */
        .staff-card {
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .staff-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid #f8f9fa;
        }

        .divider {
            width: 100%;
            height: 4px;
            background: #e83e8c;
            border-radius: 2px;
        }
    </style>
</head>

<body>
    @include('navbar')

    <!-- Header Banner -->
    <div class="header-banner">
        <div class="header-text">College Library</div>
    </div>

    <!-- Content Section -->
    <div class="container py-5">
        <div class="divider mx-auto mb-5"></div>

        <div class="row g-4 justify-content-center">
            {{-- First Row: Library Coordinator (always reserved; placeholder if none) --}}
            @php
                $coordinator = $staff->firstWhere('role', 'Library Coordinator');
            @endphp
            <div class="col-12 col-lg-10">
                <div class="card staff-card shadow rounded-4 text-center">
                    <div class="card-body">
                        <img src="{{ ($coordinator && $coordinator->photo) ? asset('storage/' . $coordinator->photo) : asset('images/placeholder.jpg') }}"
                             class="rounded-circle staff-photo mb-3" alt="{{ $coordinator ? 'Library Coordinator Photo' : 'Placeholder' }}">
                        <h4 class="fw-bold mb-1">
                            @if($coordinator)
                                {{ $coordinator->prefix }} {{ $coordinator->first_name }} {{ $coordinator->middlename ? $coordinator->middlename . ' ' : '' }}{{ $coordinator->last_name }}
                            @else
                                Position Vacant
                            @endif
                        </h4>
                        <div class="text-muted mb-2">Library Coordinator</div>
                        <div class="mb-2">
                            @if($coordinator && $coordinator->email)
                                <a href="mailto:{{ $coordinator->email }}" class="text-pink">{{ $coordinator->email }}</a>
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </div>
                        <div class="small text-secondary">{{ $coordinator ? ($coordinator->description ?: '—') : '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Second Row: Collections & Processing Librarian (always reserved; placeholder if none) --}}
            @php
                $collections = $staff->firstWhere('role', 'Collections & Processing Librarian');
            @endphp
            <div class="col-12 col-lg-10">
                <div class="card staff-card shadow rounded-4 text-center">
                    <div class="card-body">
                        <img src="{{ ($collections && $collections->photo) ? asset('storage/' . $collections->photo) : asset('images/placeholder.jpg') }}"
                             class="rounded-circle staff-photo mb-3" alt="{{ $collections ? 'Collections & Processing Librarian Photo' : 'Placeholder' }}">
                        <h4 class="fw-bold mb-1">
                            @if($collections)
                                {{ $collections->prefix }} {{ $collections->first_name }} {{ $collections->middlename ? $collections->middlename . ' ' : '' }}{{ $collections->last_name }}
                            @else
                                Position Vacant
                            @endif
                        </h4>
                        <div class="text-muted mb-2">Collections & Processing Librarian</div>
                        <div class="mb-2">
                            @if($collections && $collections->email)
                                <a href="mailto:{{ $collections->email }}" class="text-pink">{{ $collections->email }}</a>
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </div>
                        <div class="small text-secondary">{{ $collections ? ($collections->description ?: '—') : '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Remaining staff (exclude first two roles; 3 columns per row) --}}
            @foreach($staff->where('role', '!=', 'Library Coordinator')
                         ->where('role', '!=', 'Collections & Processing Librarian') as $s)
                <div class="col-md-6 col-lg-4">
                    <div class="card staff-card shadow rounded-4 text-center h-100">
                        <div class="card-body">
                            <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}"
                                 class="rounded-circle staff-photo mb-3">
                            <h5 class="fw-bold mb-1">
                                {{ $s->prefix }} {{ $s->first_name }} {{ $s->middlename ? $s->middlename . ' ' : '' }}{{ $s->last_name }}
                            </h5>
                            <div class="text-muted mb-2">{{ $s->role }}</div>
                            <div class="mb-2">
                                <a href="mailto:{{ $s->email }}" class="text-pink">{{ $s->email }}</a>
                            </div>
                            <div class="small text-secondary">{{ $s->description }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('footer')
</body>
</html>
