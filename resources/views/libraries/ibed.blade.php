<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBED Library Staff</title>
    <style>
        /* Staff Card */
        .staff-card {
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* Staff Photo */
        .staff-photo {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid #f8f9fa;
        }

        /* Divider */
        .divider {
            width: 100%;
            height: 4px;
            background: #e83e8c; /* pink */
            border-radius: 2px;
        }
    </style>
</head>
<body>
    @include('navbar')

    <div class="container py-5">
        <h2 class="fw-bold mb-3 text-center text-pink">IBED Library Staff</h2>
        <div class="divider mx-auto mb-5"></div>

        <div class="row g-4 justify-content-center">
            @foreach($staff as $s)
                <div class="col-md-6 col-lg-4">
                    <div class="card staff-card shadow rounded-4 text-center h-100">
                        <div class="card-body">
                            <img src="{{ $s->photo ? asset('storage/' . $s->photo) : asset('images/placeholder.jpg') }}"
                                 class="staff-photo mb-3">
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
