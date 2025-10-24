

@php $isGuest = Auth::check() && Auth::user()->role === 'guest'; @endphp
@if($isGuest)
<footer class="footer-main mt-auto" style="background-color: #e83e8c !important; color: #fff; padding-top: 2rem; padding-bottom: 1.5rem;">
    <div class="container">
        <div class="row gy-3 align-items-start">
            <div class="col-md-6">
                <div class="fw-bold mb-2" style="font-size:1.25rem;">Lourdes College • LC MIDES Digital Library</div>
                <div class="text-white-50">Guest access: MIDES • SIDLAK • E‑Libraries • Catalog search</div>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('mides.dashboard') }}" class="text-white me-3 text-decoration-none">MIDES</a>
                <a href="{{ route('sidlak.index') }}" class="text-white me-3 text-decoration-none">SIDLAK</a>
                <a href="{{ route('elibraries') }}" class="text-white me-3 text-decoration-none">E‑Libraries</a>
            </div>
        </div>
        <div class="mt-3 text-center text-white-75">&copy; {{ date('Y') }} Lourdes College Learning Commons</div>
    </div>
</footer>
@else
<footer class="footer-main mt-auto" style="background-color: #e83e8c !important; color: #fff; padding-top: 3.5rem; padding-bottom: 2.5rem; min-height: 340px; border-top-left-radius: 18px; border-top-right-radius: 18px;">
@php
    $contactInfo = \App\Models\ContactInfo::first();
@endphp
    <div class="container">
    <div class="row gy-4 gx-5 align-items-start">
            <!-- Column 1: Lourdes College and Slogan -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="footer-header mb-3 text-md-start text-start" style="font-size:1.5rem; font-weight:700; letter-spacing:0.5px; text-shadow:0 2px 8px rgba(232,62,140,0.08);">Lourdes College</div>
                <div class="d-flex flex-column justify-content-start align-items-md-start align-items-center">
                    <a class="footer-brand mb-2 nav-logo-animate" href="{{ (Auth::check() && Auth::user()->role === 'guest') ? route('guest.dashboard') : route('dashboard') }}" style="font-size:1.35rem; font-weight:700; letter-spacing:0.5px; display:inline-flex; align-items:center; gap:0.5rem; text-decoration:none; color:inherit;">
                        <img src="{{ asset('images/learningcommons.png') }}" alt="Logo" width="38" height="38" class="rounded" style="background:#fff; padding:2px;">
                        <span>LC MIDES Digital Library</span>
                    </a>
                    <div class="footer-motto" style="font-size:1.08rem; color:#ffe3f1; font-weight:400; letter-spacing:0.2px;">Empowering Research &amp; Learning</div>
                </div>
            </div>
            <!-- Column 2: Services -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="footer-header mb-3 text-md-start text-start" style="font-size:1.5rem; font-weight:700; letter-spacing:0.5px; text-shadow:0 2px 8px rgba(232,62,140,0.08);">Services</div>
                <div class="d-flex flex-column justify-content-start align-items-md-start align-items-start">
                    <ul class="footer-services-list list-unstyled mb-0" style="font-size:1.08rem;">
                        @auth
                        <li class="mb-1"><a href="#" class="footer-link">LiRA</a></li>
                        <li class="mb-1"><a href="{{ route('alert-services.index') }}" class="footer-link">Alert Services</a></li>
                        @endauth
                        <li class="mb-1"><a href="{{ route('alinet.form') }}" class="footer-link">ALINET</a></li>
                        <li class="mb-1"><a href="#" class="footer-link">Book Borrowing</a></li>
                        <li class="mb-1"><a href="#" class="footer-link">Information Literacy Alert Schedule</a></li>
                        <li class="mb-1"><a href="#" class="footer-link">Scanning Services</a></li>
                        <li><a href="{{ route('learning-spaces') }}" class="footer-link">Learning Spaces</a></li>
                    </ul>
                </div>
            </div>
            <!-- Column 3: Contacts -->
            <div class="col-md-4">
                <div class="footer-header mb-3 text-md-start text-start" style="font-size:1.5rem; font-weight:700; letter-spacing:0.5px; text-shadow:0 2px 8px rgba(232,62,140,0.08);">Contacts</div>
                <div class="d-flex flex-column justify-content-start align-items-md-start align-items-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <a href="{{ $contactInfo?->facebook_url ?? '#' }}" target="_blank" aria-label="Facebook" class="footer-icon-link"><i class="bi bi-facebook"></i></a>
                        <a href="mailto:{{ $contactInfo?->email ?? '' }}" aria-label="Email" class="footer-icon-link"><i class="bi bi-envelope-fill"></i></a>
                        <a href="tel:{{ $contactInfo?->phone_college ?? '' }}" aria-label="Telephone" class="footer-icon-link"><i class="bi bi-telephone-fill"></i></a>
                        <a href="tel:{{ $contactInfo?->phone_graduate ?? '' }}" aria-label="Mobile" class="footer-icon-link"><i class="bi bi-phone-fill"></i></a>
                    </div>
                    <div class="footer-contact-info" style="font-size:1.09rem;">
                        <span class="me-4 d-block mb-1"><i class="bi bi-envelope me-1"></i> <a href="mailto:{{ $contactInfo?->email ?? '' }}" class="footer-link">{{ $contactInfo?->email ?? '—' }}</a></span>
                        <span class="me-4 d-block mb-1"><i class="bi bi-telephone me-1"></i> <a href="tel:{{ $contactInfo?->phone_college ?? '' }}" class="footer-link">{{ $contactInfo?->phone_college ?? '—' }}</a></span>
                        <span class="me-4 d-block mb-1"><i class="bi bi-phone me-1"></i> <a href="tel:{{ $contactInfo?->phone_graduate ?? '' }}" class="footer-link">{{ $contactInfo?->phone_graduate ?? '—' }}</a></span>
                        <span class="d-block mb-1"><i class="bi bi-geo-alt me-1"></i> Gen. Capistrano Sts., Cagayan de Oro, Philippines, 9000</span>
                    </div>
                </div>
            </div>
        </div>
    <div class="footer-divider my-4" style="border-top:2px solid #fff; opacity:0.18;"></div>
        <div class="footer-bottom text-center" style="font-size:1.07rem; color:#fff; opacity:0.85; letter-spacing:0.2px;">
            &copy; {{ date('Y') }} Lourdes College Learning Commons. All rights reserved.
        </div>
    </div>
    <style>
        .footer-main .row > div {
            min-height: 180px;
        }
        .footer-header {
            margin-bottom: 0.75rem;
        }
        .footer-main {
            font-family: 'Segoe UI', Arial, sans-serif;
            box-shadow: 0 -2px 24px 0 rgba(31,38,135,0.10);
        }
        .footer-icon-link {
            color: #fff;
            font-size: 1.55rem;
            margin-right: 0.5rem;
            transition: color 0.18s, transform 0.18s, box-shadow 0.18s;
            display: inline-flex;
            align-items: center;
            border-radius: 50%;
            padding: 0.18rem 0.32rem;
        }
        .footer-icon-link:hover {
            color: #ffd1e3;
            background: rgba(255,255,255,0.08);
            box-shadow: 0 2px 8px rgba(232,62,140,0.12);
            transform: scale(1.13) rotate(-6deg);
            text-decoration: none;
        }
        /* Footer brand hover animation (match navbar behavior) */
        .footer-brand {
            transition: transform 0.35s cubic-bezier(.68, -0.55, .27, 1.55), text-shadow 0.35s ease;
            display: inline-flex; align-items: center; gap: .5rem;
        }

        .footer-brand:hover {
            transform: scale(1.08) translateY(-3px);
            text-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
        }

        .footer-brand img { transition: box-shadow 0.35s ease; }
        .footer-brand:hover img { box-shadow: 0 0 15px rgba(255, 255, 255, 0.5); }

        /* Footer item hover animation */
        .footer-link, .footer-services-list li a {
            transition: transform 0.18s ease, box-shadow 0.18s ease, color 0.18s ease;
            display: inline-block;
        }

        .footer-link:hover, .footer-services-list li a:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            color: #ffd1e3;
        }
        .footer-link {
            color: #fff;
            text-decoration: none;
            transition: color 0.18s, background 0.18s;
            border-radius: 4px;
            padding: 0.08rem 0.18rem;
        }
        .footer-link:hover {
            color: #ffd1e3;
            background: rgba(255,255,255,0.08);
            text-decoration: none;
        }
        .footer-divider {
            border-top: 2px solid #fff;
            opacity: 0.18;
        }
        @media (max-width: 991px) {
            .footer-main { padding-top: 2rem; padding-bottom: 1.2rem; min-height: 220px; border-radius: 0; }
            .footer-brand { font-size: 1.08rem; }
            .footer-header { font-size: 1.08rem; }
            .footer-contact-info { font-size: 0.97rem; }
            .footer-main .row > div { min-height: 100px; }
        }
        @media (max-width: 767px) {
            .footer-main { padding-top: 1.2rem; padding-bottom: 0.7rem; min-height: 180px; border-radius: 0; }
            .footer-brand { font-size: 1rem; }
            .footer-header { font-size: 1rem; }
            .footer-contact-info { font-size: 0.93rem; }
            .footer-main .row > div { min-height: 80px; }
            .footer-services-list { font-size: 0.97rem; }
        }
    </style>
</footer>
@endif
