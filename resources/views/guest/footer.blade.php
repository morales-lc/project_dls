<footer class="footer-main mt-auto" style="background-color: #e83e8c !important; color: #fff; padding-top: 2rem; padding-bottom: 1.5rem;">
    <div class="container">
        <div class="row gy-3 align-items-start">
            <div class="col-md-6">
                <div class="fw-bold mb-2" style="font-size:1.25rem;">Lourdes College • LC MIDES Digital Library</div>
                <div class="text-white-50">Guest access: MIDES • SIDLAK • E‑Libraries • ALINET • Catalog search</div>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('mides.dashboard') }}" class="text-white me-3 text-decoration-none">MIDES</a>
                <a href="{{ route('sidlak.index') }}" class="text-white me-3 text-decoration-none">SIDLAK</a>
                <a href="{{ route('elibraries') }}" class="text-white me-3 text-decoration-none">E‑Libraries</a>
                <a href="{{ route('alinet.form') }}" class="text-white me-3 text-decoration-none">ALINET</a>
            </div>
        </div>
        <div class="mt-3 text-center text-white-75">&copy; {{ date('Y') }} Lourdes College Learning Commons</div>
    </div>
</footer>