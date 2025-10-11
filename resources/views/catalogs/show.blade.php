@include('navbar')

<style>
/* --- Page Layout --- */
.catalog-page {
    padding: 3rem 0;
    color: #333;
    animation: fadeSlideIn 0.6s ease-in-out;
}

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.catalog-container {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    background: #fff;
    padding: 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    position: relative;
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
}

.back-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.back-btn a {
    font-size: 0.85rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.back-btn a:hover {
    background: #e5e7eb;
    color: #111;
}

/* --- Cover Section --- */
.catalog-cover {
    flex: 0 0 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9fafb;
    border: 1px solid #ddd;
    border-radius: 10px;
    height: auto;
    padding: 1rem;
    animation: zoomIn 0.8s ease;
    transition: transform 0.3s ease;
}
.catalog-cover:hover {
    transform: scale(1.03);
}
.catalog-cover img {
    width: 100%;
    height: auto;
    max-height: 420px;
    object-fit: contain;
    border-radius: 8px;
}

/* --- Text and Buttons --- */
.title-text {
    flex: 1;
    font-weight: 600;
    color: #1f2937;
    font-size: 1.35rem;
    line-height: 1.4;
    max-width: 70%;
    word-wrap: break-word;
    animation: fadeSlideIn 0.6s ease-in-out;
}

.button-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    margin-left: auto;
}

/* Text details */
.catalog-details p {
    margin-bottom: 0.35rem;
    line-height: 1.5;
    font-size: 0.95rem;
}

.section-title {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #d1d5db;
    padding-bottom: 0.25rem;
    width: 100%;
    position: relative;
    animation: fadeIn 1s ease;
}

.section-title::after {
    content: "";
    position: absolute;
    bottom: -1px;
    left: 0;
    height: 2px;
    width: 180px;
    background-color: #6b7280;
}

/* --- Buttons --- */
.btn-animated {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.45rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.btn-animated i {
    transition: transform 0.3s ease;
}

.btn-animated:hover i {
    transform: translateX(4px);
}

.btn-outline-dark {
    border: 1px solid #374151;
    color: #374151;
    background: transparent;
}

.btn-outline-dark:hover {
    background: #374151;
    color: #fff;
    transform: translateY(-2px);
}

.btn-pink {
    background-color: #e83e8c;
    color: #fff;
    border: none;
    box-shadow: 0 2px 6px rgba(232, 62, 140, 0.2);
}

.btn-pink:hover {
    background-color: #d63384;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(232, 62, 140, 0.4);
}

/* --- Recommended Carousel --- */
.recommend-carousel-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    overflow: hidden;
    padding: 0 1rem;
}

.recommend-carousel {
    display: flex;
    gap: 1.25rem;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    padding: 0.5rem;
    scroll-snap-type: x mandatory;

    -webkit-overflow-scrolling: touch;
}

.recommend-carousel::-webkit-scrollbar {
    height: 6px;
}
.recommend-carousel::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.recommend-card {
    flex: 0 0 calc(33.333% - 1rem);
    scroll-snap-align: start;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem .5rem .75rem .5rem;
    transition: all 0.3s ease;
    min-width: 200px;
}
.recommend-card:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    transform: translateY(-4px);
}

.card-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.rec-thumb {
    width: 90px;
    height: 120px;
    background: #f9fafb;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: .5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #eee;
}
.rec-thumb img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.rec-title {
    font-weight: 600;
    font-size: 1rem;
    text-align: center;
    margin-bottom: .25rem;
    line-height: 1.2;
}
.rec-author {
    font-size: 0.9rem;
    text-align: center;
    color: #888;
}

/* --- Arrows --- */
.carousel-arrow {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #555;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
}
.carousel-arrow.left { left: 0; }
.carousel-arrow.right { right: 0; }

.carousel-arrow:hover {
    background: #e5e7eb;
    color: #111;
    transform: translateY(-50%) scale(1.1);
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .recommend-card { flex: 0 0 calc(50% - 1rem); }
}
@media (max-width: 576px) {
    .recommend-card { flex: 0 0 80%; }
    .carousel-arrow { display: none; } /* Hide arrows on touch devices */
}


/* --- Arrows --- */
.carousel-arrow {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #555;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    margin: 0 .5rem;
}
.carousel-arrow:hover {
    background: #e5e7eb;
    color: #111;
    transform: scale(1.1);
}

/* --- Responsive --- */
@media (max-width: 720px) {
    .catalog-container { flex-direction: column; padding: 1rem; }
    .catalog-cover { width: 100%; max-width: 360px; margin: 0 auto; }
    .top-line { align-items: center; gap: .5rem; flex-direction: column; }
    .title-text { max-width: 100%; text-align: center; }
    .button-group { margin-left: 0; justify-content: center; }
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes zoomIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<div class="container catalog-page">
    <div class="catalog-container">
        <div class="back-btn">
            <a href="{{ url('/catalogs') }}" class="btn btn-outline-secondary btn-sm btn-animated">
                <i class="bi bi-arrow-left"></i> Back to Search
            </a>
        </div>

        <div class="catalog-cover">
            <img src="{{ $catalog->cover_image ? asset('storage/' . $catalog->cover_image) : asset('images/book-placeholder.png') }}"
                alt="Book Cover">
        </div>

        <div class="catalog-details">
            <div class="top-line d-flex align-items-start justify-content-between flex-wrap">
                <h4 class="title-text">{{ $catalog->title }}</h4>
                <div class="button-group">
                    <a href="#" class="btn btn-outline-dark btn-sm btn-animated">
                        <i class="bi bi-plus-circle"></i> Add to list
                    </a>
                    <a href="{{ route('lira.jotform', [
                        'title' => $catalog->title,
                        'author' => $catalog->author,
                        'call_number' => $catalog->call_number,
                        'isbn' => $catalog->isbn,
                        'lccn' => $catalog->lccn,
                        'issn' => $catalog->issn
                    ]) }}" class="btn btn-pink btn-sm btn-animated">
                        <i class="bi bi-send"></i> Request
                    </a>
                </div>
            </div>

            <div class="section-title">Catalog Information</div>
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr><th>Title</th><td>{{ $catalog->title }}</td></tr>
                        <tr><th>Author</th><td>{{ $catalog->author ?? '-' }}</td></tr>
                        <tr><th>Call Number</th><td>{{ $catalog->call_number ?? '-' }}</td></tr>
                        <tr><th>Sub Location</th><td>{{ $catalog->sublocation ?? '-' }}</td></tr>
                        <tr><th>Publisher</th><td>{{ $catalog->publisher ?? '-' }}</td></tr>
                        <tr><th>Year</th><td>{{ $catalog->year ?? '-' }}</td></tr>
                        <tr><th>Edition</th><td>{{ $catalog->edition ?? '-' }}</td></tr>
                        <tr><th>Format</th><td>{{ $catalog->format ?? '-' }}</td></tr>
                        <tr><th>Content Type</th><td>{{ $catalog->content_type ?? '-' }}</td></tr>
                        <tr><th>Media Type</th><td>{{ $catalog->media_type ?? '-' }}</td></tr>
                        <tr><th>Carrier Type</th><td>{{ $catalog->carrier_type ?? '-' }}</td></tr>
                        <tr><th>ISBN</th><td>{{ $catalog->isbn ?? '-' }}</td></tr>
                        <tr><th>ISSN</th><td>{{ $catalog->issn ?? '-' }}</td></tr>
                        <tr><th>LCCN</th><td>{{ $catalog->lccn ?? '-' }}</td></tr>
                        <tr><th>Subjects</th><td>{{ $catalog->subjects ?? '-' }}</td></tr>
                        <tr><th>Additional Details</th><td style="white-space: pre-line;">{!! nl2br(e($catalog->additional_details ?? '-')) !!}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="section-title mb-3">Recommended for You</div>
        <div class="recommend-carousel-container">
            <button class="carousel-arrow left" id="recLeft" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
            <div class="recommend-carousel" id="recommendCarousel">
                @foreach($recommendations as $rec)
                    <div class="recommend-card">
                        <a href="{{ route('catalogs.show', $rec->id) }}" class="card-link">
                            <div class="rec-thumb">
                                <img src="{{ $rec->cover_image ? asset('storage/' . $rec->cover_image) : asset('images/book-placeholder.png') }}" alt="Cover">
                            </div>
                            <div class="rec-info">
                                <div class="rec-title">{{ Str::limit($rec->title, 40) }}</div>
                                <div class="rec-author text-muted small">{{ $rec->author ?? 'Unknown Author' }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <button class="carousel-arrow right" id="recRight" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>

<div style="height: 120px;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('recommendCarousel');
    const leftBtn = document.getElementById('recLeft');
    const rightBtn = document.getElementById('recRight');

    const scrollAmount = 300;
    leftBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
    rightBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
});
</script>


@include('footer')
