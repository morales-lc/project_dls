<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Library</title>
    <style>
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
            /* Increased from 150px */
            height: 200px;
            /* Increased from 150px */
            object-fit: cover;
            border: 6px solid #f8f9fa;
        }

        .divider {
            width: 100%;
            height: 4px;
            background: #e83e8c;
            /* pink */
            border-radius: 2px;
        }
    </style>
</head>

<body>
    @include('navbar')

    <!-- Content Section -->
    <div class="container py-5">
        <h2 class="fw-bold mb-3 text-center text-pink">Meet Our College Library Staff</h2>
        <div class="divider mx-auto mb-5"></div>

        <div class="row g-4 justify-content-center">
            {{-- First Row: Library Coordinator --}}
            @php
            $coordinator = $staff->firstWhere('role', 'Library Coordinator');
            @endphp
            @if($coordinator)
            <div class="col-12 col-lg-10">
                <div class="card staff-card shadow rounded-4 text-center">
                    <div class="card-body">
                        <img src="{{ $coordinator->photo ? asset('storage/' . $coordinator->photo) : asset('images/placeholder.jpg') }}"
                            class="rounded-circle staff-photo mb-3">
                        <h4 class="fw-bold mb-1">{{ $coordinator->prefix }} {{ $coordinator->first_name }} {{ $coordinator->middlename ? $coordinator->middlename . ' ' : '' }}{{ $coordinator->last_name }}</h4>
                        <div class="text-muted mb-2">{{ $coordinator->role }}</div>
                        <div class="mb-2"><a href="mailto:{{ $coordinator->email }}" class="text-pink">{{ $coordinator->email }}</a></div>
                        <div class="small text-secondary">{{ $coordinator->description }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Second Row: Collections & Processing Librarian --}}
            @php
            $collections = $staff->firstWhere('role', 'Collections & Processing Librarian');
            @endphp
            @if($collections)
            <div class="col-12 col-lg-10">
                <div class="card staff-card shadow rounded-4 text-center">
                    <div class="card-body">
                        <img src="{{ $collections->photo ? asset('storage/' . $collections->photo) : asset('images/placeholder.jpg') }}"
                            class="rounded-circle staff-photo mb-3">
                        <h4 class="fw-bold mb-1">{{ $collections->prefix }} {{ $collections->first_name }} {{ $collections->middlename ? $collections->middlename . ' ' : '' }}{{ $collections->last_name }}</h4>
                        <div class="text-muted mb-2">{{ $collections->role }}</div>
                        <div class="mb-2"><a href="mailto:{{ $collections->email }}" class="text-pink">{{ $collections->email }}</a></div>
                        <div class="small text-secondary">{{ $collections->description }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Remaining staff (excluding the two above) in rows of 3 --}}
            @foreach($staff->where('id', '!=', optional($coordinator)->id)
            ->where('id', '!=', optional($collections)->id) as $s)
            <div class="col-md-6 col-lg-4">
                <div class="card staff-card shadow rounded-4 text-center h-100">
                    <div class="card-body">
                        <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}"
                            class="rounded-circle staff-photo mb-3">
                        <h5 class="fw-bold mb-1">{{ $s->prefix }} {{ $s->first_name }} {{ $s->middlename ? $s->middlename . ' ' : '' }}{{ $s->last_name }}</h5>
                        <div class="text-muted mb-2">{{ $s->role }}</div>
                        <div class="mb-2"><a href="mailto:{{ $s->email }}" class="text-pink">{{ $s->email }}</a></div>
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