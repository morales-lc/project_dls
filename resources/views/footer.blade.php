<footer class="footer-main mt-auto" style="background: #e83e8c; color: #fff; padding-top: 2rem; padding-bottom: 1rem;">
    <div class="container">
        <div class="row align-items-center gy-3">
            <div class="col-md-7 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <a href="https://facebook.com/LourdesCollegeLibrary" target="_blank" aria-label="Facebook" class="footer-icon-link"><i class="bi bi-facebook"></i></a>
                    <a href="mailto:library@lccdo.edu.ph" aria-label="Email" class="footer-icon-link"><i class="bi bi-envelope-fill"></i></a>
                    <a href="tel:+63888584093" aria-label="Telephone" class="footer-icon-link"><i class="bi bi-telephone-fill"></i></a>
                    <a href="tel:+639123456789" aria-label="Mobile" class="footer-icon-link"><i class="bi bi-phone-fill"></i></a>
                </div>
                <div class="footer-contact-info" style="font-size:1.08rem;">
                    <span class="me-4"><i class="bi bi-envelope me-1"></i> <a href="mailto:library@lccdo.edu.ph" class="footer-link">library@lccdo.edu.ph</a></span>
                    <span class="me-4"><i class="bi bi-telephone me-1"></i> <a href="tel:+63888584093" class="footer-link">(088) 858-4093 loc. 123</a></span>
                    <span><i class="bi bi-phone me-1"></i> <a href="tel:+639123456789" class="footer-link">0912-345-6789</a></span>
                </div>
            </div>
            <div class="col-md-5 text-md-end text-center">
                <div class="footer-brand mb-1" style="font-size:1.25rem; font-weight:700; letter-spacing:0.5px;">
                    <i class="bi bi-journal-bookmark-fill me-2" style="color:#fff;"></i>Lourdes College Library
                </div>
                <div class="footer-motto" style="font-size:1.05rem; color:#fff; font-weight:400;">Empowering Research &amp; Learning</div>
            </div>
        </div>
        <div class="footer-divider my-3" style="border-top:1.5px solid #fff; opacity:0.25;"></div>
        <div class="footer-bottom text-center" style="font-size:1.02rem; color:#fff; opacity:0.85;">
            &copy; {{ date('Y') }} Lourdes College Library. All rights reserved.
        </div>
    </div>
    <style>
        .footer-main {
            font-family: 'Segoe UI', Arial, sans-serif;
            box-shadow: 0 -2px 16px 0 rgba(31,38,135,0.08);
        }
        .footer-icon-link {
            color: #fff;
            font-size: 1.45rem;
            margin-right: 0.5rem;
            transition: color 0.18s, transform 0.18s;
            display: inline-flex;
            align-items: center;
        }
        .footer-icon-link:hover {
            color: #ffd1e3;
            transform: scale(1.12) rotate(-6deg);
            text-decoration: none;
        }
        .footer-link {
            color: #fff;
            text-decoration: underline dotted #fff 1.5px;
            transition: color 0.18s;
        }
        .footer-link:hover {
            color: #ffd1e3;
            text-decoration: underline solid #ffd1e3 2px;
        }
        .footer-divider {
            border-top: 1.5px solid #fff;
            opacity: 0.25;
        }
        @media (max-width: 767px) {
            .footer-main { padding-top: 1rem; padding-bottom: 0.5rem; }
            .footer-brand { font-size: 1rem; }
            .footer-contact-info { font-size: 0.95rem; }
        }
    </style>
</footer>
