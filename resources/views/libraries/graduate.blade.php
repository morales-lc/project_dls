<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graduate Library Staff</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <!-- Bootstrap Icons for resource cards -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .header-banner {
            position: relative;
            width: 100%;
            height: 300px;
            background: url('{{ asset("images/graduate_library.png") }}') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .header-banner::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.4); /* dark overlay */
        }
        .header-text {
            position: relative;
            z-index: 2;
            font-size: 2.2rem;
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }
        .staff-card {
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .staff-photo {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid #f8f9fa;
        }
        .divider {
            width: 100%;
            height: 4px;
            background: #e83e8c; /* pink */
            border-radius: 2px;
        }

        /* Resources section cards */
        .resource-card {
            transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
            cursor: pointer;
            border-radius: 1rem;
        }
        .resource-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
            background: #fff5f7;
        }
        .resource-icon {
            width: 72px;
            height: 72px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffe6ef, #f3f7ff);
            color: #e83e8c;
            font-size: 2rem;
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            transition: transform 0.22s ease;
        }
        .resource-card:hover .resource-icon { transform: scale(1.05); }
        .resource-title { color:#e83e8c; font-weight: 700; }
        .resource-open-pill {
            border:1.5px solid #e83e8c; color:#e83e8c; pointer-events:none;
        }
    </style>
</head>
<body>
    @include('navbar')

    <!-- Header Banner -->
    <div class="header-banner">
        <div class="header-text">Graduate Library</div>
    </div>

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

    <!-- Resources Section -->
    <div class="container py-4">
        <div class="divider mx-auto mb-3"></div>
        <h3 class="fw-bold text-center mb-3" style="letter-spacing:1px;">Resources</h3>
        <div class="divider mx-auto mb-4"></div>
        @auth
        <div class="row g-4 justify-content-center">
            <!-- MIDES (Graduate) -->
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('mides.graduate.categories') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm text-center border-0 resource-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <div class="resource-icon mb-3"><i class="bi bi-journal-bookmark"></i></div>
                            <h5 class="mt-1 mb-1 resource-title">MIDES Repository</h5>
                            <div class="text-muted small">Graduate theses and related research</div>
                            <span class="btn mt-3 rounded-pill px-4 resource-open-pill">Open</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- SIDLAK Journals -->
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('sidlak.index') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm text-center border-0 resource-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <div class="resource-icon mb-3"><i class="bi bi-journal-text"></i></div>
                            <h5 class="mt-1 mb-1 resource-title">SIDLAK Journals</h5>
                            <div class="text-muted small">Multidisciplinary research journal</div>
                            <span class="btn mt-3 rounded-pill px-4 resource-open-pill">Open</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Online Databases -->
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('elibraries') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm text-center border-0 resource-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                            <div class="resource-icon mb-3"><i class="bi bi-database-gear"></i></div>
                            <h5 class="mt-1 mb-1 resource-title">Online Databases</h5>
                            <div class="text-muted small">Licensed resources and platforms</div>
                            <span class="btn mt-3 rounded-pill px-4 resource-open-pill">Open</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endauth
    </div>

    @include('footer')
</body>
</html>
