<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Lourdes College Learning Commons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    @include('navbar')
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow p-5 mb-5" style="border: 2.5px solid #d81b60; background: #fff6fa;">
                    <h2 class="fw-bold text-center mb-4" style="color:#d81b60; font-size:2.5rem; letter-spacing:1px;">Contact Us</h2>
                    
                    <!-- Library Contact Information -->
                    <div class="row mb-5">
                        <div class="col-lg-6">
                            <h4 class="fw-bold text-pink mb-4">Library Contact Numbers</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold">College Library</td>
                                            <td class="text-end">
                                                <a href="tel:{{ $contact?->phone_college }}" class="text-decoration-none text-pink">
                                                    <i class="bi bi-telephone me-2"></i>{{ $contact?->phone_college ?? '—' }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Graduate Library</td>
                                            <td class="text-end">
                                                <a href="tel:{{ $contact?->phone_graduate }}" class="text-decoration-none text-pink">
                                                    <i class="bi bi-telephone me-2"></i>{{ $contact?->phone_graduate ?? '—' }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Senior High School Library</td>
                                            <td class="text-end">
                                                <a href="tel:{{ $contact?->phone_senior_high }}" class="text-decoration-none text-pink">
                                                    <i class="bi bi-telephone me-2"></i>{{ $contact?->phone_senior_high ?? '—' }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">IBED Library</td>
                                            <td class="text-end">
                                                <a href="tel:{{ $contact?->phone_ibed }}" class="text-decoration-none text-pink">
                                                    <i class="bi bi-telephone me-2"></i>{{ $contact?->phone_ibed ?? '—' }}
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <h4 class="fw-bold text-pink mb-4">Social Media & Online</h4>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-facebook text-primary me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <div class="fw-semibold">Facebook Page</div>
                                        @if(!empty($contact?->facebook_url))
                                        <a href="{{ $contact->facebook_url }}" target="_blank" class="text-decoration-none text-pink">
                                            {{ parse_url($contact->facebook_url, PHP_URL_HOST) ?? $contact->facebook_url }}
                                        </a>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope text-danger me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <div class="fw-semibold">Email</div>
                                        @if(!empty($contact?->email))
                                        <a href="mailto:{{ $contact->email }}" class="text-decoration-none text-pink">
                                            {{ $contact->email }}
                                        </a>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-globe text-success me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <div class="fw-semibold">Lourdes College Inc. Website</div>
                                        @if(!empty($contact?->website_url))
                                        <a href="{{ $contact->website_url }}" target="_blank" class="text-decoration-none text-pink">
                                            {{ parse_url($contact->website_url, PHP_URL_HOST) ?? $contact->website_url }}
                                        </a>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Library Staff Section -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="fw-bold text-pink mb-4 text-center">Library Staff</h4>
                            
                            @if($libraryStaff->count() > 0)
                                <div class="row g-4">
                                    @foreach($libraryStaff as $staff)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff 80%, #ffe3f1 100%);">
                                                <div class="card-body text-center">
                                                    <div class="mb-3">
                                                        @if($staff->photo)
                                                            <img src="{{ asset('storage/' . $staff->photo) }}" 
                                                                 alt="{{ $staff->first_name }} {{ $staff->last_name }}" 
                                                                 class="rounded-circle mx-auto d-block" 
                                                                 style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #d81b60;">
                                                        @else
                                                            <div class="rounded-circle mx-auto d-block d-flex align-items-center justify-content-center" 
                                                                 style="width: 80px; height: 80px; background: #ffd1e3; color: #d81b60; border: 3px solid #d81b60;">
                                                                <i class="bi bi-person" style="font-size: 2rem;"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <h6 class="fw-bold text-pink mb-1">
                                                        {{ $staff->prefix }} {{ $staff->first_name }} {{ $staff->middlename ? $staff->middlename . ' ' : '' }}{{ $staff->last_name }}
                                                    </h6>
                                                    
                                                    <p class="text-muted small mb-2">{{ $staff->role }}</p>
                                                    
                                                    <p class="text-muted small mb-2">
                                                        {{ ucfirst(str_replace('_', ' ', $staff->department)) }} Library
                                                    </p>
                                                    
                                                    @if($staff->email)
                                                        <a href="mailto:{{ $staff->email }}" class="text-decoration-none text-pink small">
                                                            <i class="bi bi-envelope me-1"></i>{{ $staff->email }}
                                                        </a>
                                                    @endif
                                                    
                                                    @if($staff->description)
                                                        <p class="text-muted small mt-2">{{ $staff->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-people" style="font-size: 3rem;"></i>
                                    <p class="mt-2">No library staff information available at the moment.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('footer')
</body>
</html>

