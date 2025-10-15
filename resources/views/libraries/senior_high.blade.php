<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior High School Library Staff</title>
    <style>
        /* Header Banner */
        .header-banner {
            position: relative;
            width: 100%;
            height: 300px;
            background: url('{{ asset("images/IMG_1462.JPG") }}') center center/cover no-repeat;
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
            /* dark overlay */
        }

        .header-text {
            position: relative;
            z-index: 2;
            font-size: 2.2rem;
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        /* Staff Card */
        .staff-card {
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

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
            background: #e83e8c;
            /* pink */
            border-radius: 2px;
        }

        .btn.btn-pink,
        a.btn-pink {
            background-color: #e83e8c !important;
            color: #fff !important;
            border: none !important;
            background-image: none !important;
            box-shadow: none !important;
            text-decoration: none !important;
            padding: .5rem 1.25rem !important;
            font-size: 1.1rem !important;
            border-radius: .5rem !important;
            transition: background-color .25s ease, transform .15s ease, box-shadow .15s ease;
        }

        /* Hover / focus */
        .btn.btn-pink:hover,
        a.btn-pink:hover,
        .btn.btn-pink:focus,
        a.btn-pink:focus {
            background-color: #d63384 !important;
            color: #fff !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 18px rgba(214, 51, 132, 0.18) !important;
        }

        /* Active state */
        .btn.btn-pink:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
</head>

<body>
    @include('navbar')

    <!-- Header Banner -->
    <div class="header-banner">
        <div class="header-text">Senior High School Library Staff</div>
    </div>

    <!-- Staff Section -->
    <div class="container py-5">
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

    <!-- Services Section -->
    <div class="container py-4">
        <div class="divider mx-auto mb-3"></div>
        <h3 class="fw-bold text-center mb-3" style="letter-spacing:1px;">Services</h3>
        <div class="divider mx-auto mb-4"></div>
        <div class="row align-items-center justify-content-center g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h4 class="fw-bold mb-3">Senior High School Research Paper</h4>
                    <div class="mb-3 text-muted" style="font-size:1.08rem;">
                        This page provides access to abstracts, introduction and related literature of the research paper completed for the senior high school department in Lourdes College.
                    </div>
                    <a href="{{ route('mides.seniorhigh.programs') }}"
                        class="btn btn-pink px-4 py-2 fw-bold"
                        style="font-size:1.1rem;">
                        Mides Repository
                    </a>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-center">
                <img src="{{ asset('images/digital.jpg') }}" alt="Digital Library" class="img-fluid rounded-4 shadow" style="max-width:380px;">
            </div>
        </div>
    </div>

    
    <!-- Alert Services Covers (Senior Highschool) -->
    <div class="container py-5">
        <div class="divider mx-auto mb-3"></div>
        <h4 class="fw-bold text-center mb-3">Alert Services</h4>
        <div class="divider mx-auto mb-3"></div>
        

        @if(isset($covers) && $covers->count())
        <div class="row g-4 justify-content-center">
            @foreach($covers as $cover)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <a href="{{ $cover->pdf_path ? asset('storage/' . $cover->pdf_path) : '#' }}" target="_blank" class="d-block text-decoration-none text-dark">
                        <img src="{{ $cover->cover_image ? asset('storage/' . $cover->cover_image) : asset('images/placeholder.jpg') }}" class="img-fluid rounded-top" style="height:260px; object-fit:cover; width:100%;">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">{{ $cover->title ?? 'Untitled' }}</h6>
                        
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center text-muted">No recent alert covers available.</div>
        @endif

    </div>

    @include('footer')
</body>

</html>