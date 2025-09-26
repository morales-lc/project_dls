<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/news-card.css') }}" rel="stylesheet">
</head>

<body style="min-height: 100vh; overflow-y: auto; background-color: #f8f9fa;">

<!-- Navbar -->
@include('navbar')



<!-- Content -->
    <div class="container mt-5 mb-5">

    <h2 class="fw-bold mb-4">Welcome to Lourdes College Library!</h2>
    <div class="input-group search-box mb-5">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" placeholder="Search books, journals, authors...">
        <select class="form-select">
            <option selected>SELECT TYPE</option>
            <option>Book</option>
            <option>eBook</option>
            <option>Journal</option>
            <option>Thesis</option>
            <option>Dissertation</option>
            <option>Map</option>
            <option>Multimedia</option>
        </select>
        <a class="btn btn-secondary" href="{{ route('search') }}">Search</a>
    </div>


    <!-- Announcements & Library Hours Side by Side -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <section class="section-white rounded-4 shadow-sm p-4 h-100">
                <h4 class="fw-bold mb-3 text-pink">Library Announcements</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent">Library Orientation for Freshmen: August 15, 2025, 9:00 AM at the Main Library Hall.</li>
                    <li class="list-group-item bg-transparent">New Arrivals: Over 200 new books in Science, Business, and Literature sections!</li>
                    <li class="list-group-item bg-transparent">Library will be closed on August 21 for a public holiday.</li>
                </ul>
            </section>
        </div>
        <div class="col-lg-4 d-flex align-items-stretch">
            <div class="section-white rounded-4 shadow-sm p-4 w-100 d-flex flex-column align-items-center justify-content-center">
                <h4 class="fw-bold mb-3">Library Hours</h4>
                <img src="{{ asset('images/servicehours.gif') }}" alt="Library Service Hours" class="img-fluid rounded shadow library-hours-gif" style="max-width: 340px; width: 100%; height: auto; background: #fff; border: 2px solid #ffd1e3; padding: 0.5rem; transition: transform 0.35s cubic-bezier(.4,1.6,.6,1);" loading="lazy">
            </div>
        </div>
    </div>


    <!-- Posts by Type -->

    @php
        $types = ['Announcement', 'Event', 'Update', 'Post'];
    @endphp
    @foreach($types as $i => $type)
    <section class="{{ $i % 2 == 0 ? 'section-white' : 'section-pink' }} rounded-4 shadow-sm p-4 mb-5 position-relative" style="border:2.5px solid #4a90e2;">
        <h4 class="fw-bold mb-3 {{ $i % 2 == 0 ? 'text-pink' : 'text-white' }}">{{ $type == 'Post' ? 'Latest Posts' : $type . 's' }}</h4>
        <div class="news-carousel-wrap">
            @php
                $cardCount = isset($posts) ? $posts->where('type', $type)->count() : 0;
            @endphp
            @if($cardCount > 3)
                <button class="carousel-btn modern-btn left" type="button" aria-label="Scroll left" tabindex="0">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="14" cy="14" r="13" stroke="#d81b60" stroke-width="2.5" fill="#fff"/>
                        <path d="M16.5 9L12 14L16.5 19" stroke="#d81b60" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif
            <div class="news-carousel" id="news-carousel-{{ $i }}">
                @if(isset($posts) && $posts->where('type', $type)->count())
                    @foreach($posts->where('type', $type)->values() as $j => $post)
                    <div class="carousel-card">
                        <div class="lc-news-card card-clickable" tabindex="0" role="button"
                            data-title="{{ e($post->title) }}"
                            data-type="{{ e($post->type) }}"
                            data-description="{{ e($post->description) }}"
                            data-photo="{{ $post->photo ? asset('storage/' . $post->photo) : '' }}"
                            data-youtube="{{ $post->youtube_link ?? '' }}"
                            data-website="{{ $post->website_link ?? '' }}"
                            data-ogthumb="{{ $post->og_image ?? '' }}"
                            data-favicon="{{ $post->website_link ? (parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST) . '/favicon.ico') : '' }}"
                            data-placeholder="{{ asset('images/placeholder.jpg') }}"
                        >
                            @if($post->photo)
                                <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="lc-news-card-img">
                            @elseif($post->youtube_link)
                                @php
                                    preg_match('/v=([^&]+)/', $post->youtube_link, $matches);
                                    $ytid = $matches[1] ?? null;
                                @endphp
                                @if($ytid)
                                    <iframe class="lc-news-card-img" src="https://www.youtube.com/embed/{{ $ytid }}" title="YouTube video" allowfullscreen style="height:180px;"></iframe>
                                @endif
                            @elseif($post->website_link)
                                @php
                                    $ogThumb = $post->og_image ?? null;
                                    $favicon = parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST) . '/favicon.ico';
                                @endphp
                                <img src="{{ $ogThumb ?: $favicon }}" alt="Website Thumbnail" class="lc-news-card-img">
                            @else
                                <img src="{{ asset('images/placeholder.jpg') }}" alt="No Image" class="lc-news-card-img">
                            @endif
                            <div class="lc-news-card-body">
                                <div class="mb-2">
                                    <span class="badge {{ $i % 2 == 0 ? 'bg-pink text-white' : 'bg-white text-pink' }}">{{ $post->type }}</span>
                                </div>
                                <div class="lc-news-card-title" style="display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-overflow:ellipsis; min-height:2.7em; font-size:1.13rem; line-height:1.35;">{{ $post->title }}</div>
                                <div class="lc-news-card-desc">{{ $post->description }}</div>
                                @if($post->website_link)
                                    <a href="{{ $post->website_link }}" target="_blank" class="lc-news-card-btn" onclick="event.stopPropagation();">Read More</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="carousel-card w-100 text-center text-muted" style="min-width:220px;">No {{ $type == 'Post' ? 'posts' : strtolower($type) . 's' }} yet.</div>
                @endif
            </div>
            @if($cardCount > 3)
                <button class="carousel-btn modern-btn right" type="button" aria-label="Scroll right" tabindex="0">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="14" cy="14" r="13" stroke="#d81b60" stroke-width="2.5" fill="#fff"/>
                        <path d="M11.5 9L16 14L11.5 19" stroke="#d81b60" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif
        </div>
        <div class="carousel-dots mt-3 text-center" id="carousel-dots-{{ $i }}"></div>
    </section>
    @endforeach

    <!-- Featured Resources -->
    <div class="mb-5">
        <h4 class="fw-bold">Featured Resources</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Online Journal Access</h5>
                        <p class="card-text">Access thousands of academic journals and research papers through our digital library subscriptions.</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Explore Journals</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Library Guide</h5>
                        <p class="card-text">Learn how to use the library catalog, borrow books, and access e-resources with our step-by-step guide.</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">View Guide</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Ask a Librarian</h5>
                        <p class="card-text">Need help with your research? Chat or email with our librarians for assistance.</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Post Modal -->
        <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content post-modal-glass animate-modal">
                    <div class="modal-header border-0 post-modal-header">
                        <div class="d-flex align-items-center w-100">
                            <div class="flex-grow-1">
                                <h5 class="modal-title fw-bold mb-0" id="postModalLabel"></h5>
                                <span id="postModalType" class="badge bg-pink text-white ms-1 mt-2"></span>
                            </div>
                            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0">
                        <div id="postModalImageWrap" class="w-100 post-modal-imgwrap"></div>
                        <div class="p-4 post-modal-body">
                            <div id="postModalDesc" class="mb-3"></div>
                            <div id="postModalLinks" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .card-clickable { cursor: pointer; transition: box-shadow .2s, transform .2s; }
        .card-clickable:focus, .card-clickable:hover { box-shadow: 0 4px 24px 0 rgba(216,27,96,0.18), 0 1.5px 8px 0 rgba(66,46,89,0.08); transform: translateY(-2px) scale(1.02); outline: 2px solid #d81b60; }

        /* Uniform card image and video size */
        .lc-news-card-img,
        .lc-news-card .lc-news-card-img,
        .lc-news-card iframe.lc-news-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 0 !important;
            background: #fff;
            display: block;
        }
        .lc-news-card iframe.lc-news-card-img {
            min-height: 180px;
            max-height: 180px;
        }

        /* Modern modal, no gradients, pink highlights only */
        .post-modal-glass {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 8px 40px 0 rgba(216,27,96,0.13), 0 2px 16px 0 rgba(66,46,89,0.08);
            border: 2px solid #ffd1e3;
            overflow: hidden;
        }
        .animate-modal {
            animation: modalPop .35s cubic-bezier(.4,1.6,.6,1) 1;
        }
        @keyframes modalPop {
            0% { transform: scale(.92) translateY(40px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }
        .post-modal-header {
            background: #ffe3ef;
            border-bottom: 1.5px solid #ffd1e3;
            padding-top: 1.2rem;
            padding-bottom: 1.2rem;
        }
        #postModal .modal-title {
            color: #d81b60;
            font-size: 2.1rem;
            letter-spacing: -0.5px;
        }
        #postModalType {
            font-size: 1rem;
            letter-spacing: 0.5px;
            background: #d81b60 !important;
            border-radius: 0.7rem;
            padding: 0.4em 1.1em;
            font-weight: 600;
            box-shadow: 0 2px 8px 0 rgba(216,27,96,0.08);
        }
        .post-modal-imgwrap {
            background: #ffe3ef;
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            overflow: hidden;
        }
        #postModalImageWrap img, #postModalImageWrap iframe {
            width: 100%;
            height: 320px;
            min-height: 180px;
            max-height: 320px;
            object-fit: cover;
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            box-shadow: 0 2px 16px 0 rgba(216,27,96,0.08);
            background: #fff;
            display: block;
        }
        #postModalImageWrap iframe {
            aspect-ratio: 16/9;
        }
        @media (max-width: 991px) {
            #postModalImageWrap img, #postModalImageWrap iframe {
                height: 180px;
                max-height: 180px;
            }
        }
        .post-modal-body {
            font-size: 1.18rem;
            color: #a0003a;
            line-height: 1.7;
        }
        #postModalDesc {
            font-size: 1.18rem;
            color: #a0003a;
            line-height: 1.7;
            word-break: break-word;
        }
        #postModalLinks a {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 2em;
            font-weight: 600;
            padding: 0.5em 1.5em;
            font-size: 1.05rem;
            box-shadow: 0 2px 8px 0 rgba(216,27,96,0.08);
            transition: background .18s, color .18s, box-shadow .18s;
        }
        #postModalLinks a.btn-primary {
            background: #d81b60;
            border: none;
            color: #fff;
        }
        #postModalLinks a.btn-primary:hover {
            background: #b8004c;
            color: #fff;
        }
        #postModalLinks a.btn-danger {
            background: #ff5252;
            border: none;
            color: #fff;
        }
        #postModalLinks a.btn-danger:hover {
            background: #b8004c;
            color: #fff;
        }
        @media (max-width: 767px) {
            #postModal .modal-title { font-size: 1.2rem; }
            #postModalImageWrap img, #postModalImageWrap iframe { max-height: 180px; }
            .post-modal-glass { border-radius: 0.8rem; }
            .post-modal-header { border-radius: 0.8rem 0.8rem 0 0; }
        }
        /* Reduce horizontal gutter between news cards even further */
        .news-carousel-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .news-carousel {
            display: flex;
            justify-content: flex-start;
            overflow-x: hidden;
            scroll-behavior: smooth;
            gap: 0.7rem;
            padding-bottom: 0.5rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            width: 100%;
            max-width: 100%;
        }
        .news-carousel::-webkit-scrollbar { display: none; }
        .carousel-card {
            flex: 0 0 calc((100% - 1.4rem) / 3); /* 3 cards, 2 gaps of 0.7rem */
            max-width: calc((100% - 1.4rem) / 3);
            min-width: calc((100% - 1.4rem) / 3);
            margin-right: 0;
        }
        .lc-news-card {
            border-radius: 0 !important;
        }
        @media (max-width: 991px) {
            .carousel-card {
                flex: 0 0 90vw;
                max-width: 90vw;
                min-width: 90vw;
            }
        }
        .carousel-btn {
            position: absolute;
            top: 50%;
            z-index: 2;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 0;
            width: 3.2rem;
            height: 3.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: filter .18s, box-shadow .18s;
            box-shadow: none;
        }
        .carousel-btn.left { left: -1.6rem; }
        .carousel-btn.right { right: -1.6rem; }
        .carousel-btn:active svg circle,
        .carousel-btn:focus svg circle {
            stroke: #b8004c;
            filter: drop-shadow(0 2px 8px #ffd1e3);
        }
        .carousel-btn[disabled] { opacity: 0.4; pointer-events: none; }
        .carousel-btn svg { display: block; }
        .carousel-btn svg circle { transition: stroke .18s, filter .18s; }
        .carousel-btn svg path { transition: stroke .18s; }
        .carousel-btn:active svg path,
        .carousel-btn:focus svg path {
            stroke: #b8004c;
        }
        .carousel-btn.modern-btn {
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
        }
        .carousel-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .carousel-dot {
            width: 0.8rem;
            height: 0.8rem;
            border-radius: 50%;
            background: #ffd1e3;
            border: 2px solid #d81b60;
            cursor: pointer;
            transition: background .18s, border .18s;
        }
        .carousel-dot.active {
            background: #d81b60;
            border-color: #d81b60;
        }
        .section-white {
            background: #fff;
        }
        .section-pink {
            background: #ffb6c1;
        }
        .text-pink {
            color: #d81b60 !important;
        }
        .bg-pink {
            background: #d81b60 !important;
        }
        .section-pink .card {
            background: #fff;
        }
        .section-pink .text-white {
            color: #fff !important;
        }
        .section-pink .badge.bg-white.text-pink {
            background: #fff !important;
            color: #d81b60 !important;
        }
        .section-white .badge.bg-pink.text-white {
            background: #d81b60 !important;
            color: #fff !important;
        }
    .library-hours-gif:hover {
        transform: scale(1.13) rotate(-2deg);
        z-index: 2;
        box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18), 0 2px 16px 0 rgba(216,27,96,0.10);
    }
    </style>

    <!-- Library Hours moved above -->

    <!-- Library Services -->
    <div class="mb-5">
        <h4 class="fw-bold">Library Services</h4>
        <ul class="list-group">
            <li class="list-group-item">Book Borrowing & Returning</li>
            <li class="list-group-item">Reference and Research Assistance</li>
            <li class="list-group-item">Internet and Computer Access</li>
            <li class="list-group-item">Printing and Scanning</li>
            <li class="list-group-item">Study Rooms and Discussion Areas</li>
            <li class="list-group-item">Online Resources and Databases</li>
        </ul>
    </div>


</div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal logic (unchanged)
            var postModal = new bootstrap.Modal(document.getElementById('postModal'));
            document.querySelectorAll('.card-clickable').forEach(function(card) {
                card.addEventListener('click', function() {
                    var title = card.getAttribute('data-title') || '';
                    var type = card.getAttribute('data-type') || '';
                    var desc = card.getAttribute('data-description') || '';
                    var photo = card.getAttribute('data-photo') || '';
                    var youtube = card.getAttribute('data-youtube') || '';
                    var website = card.getAttribute('data-website') || '';
                    var ogthumb = card.getAttribute('data-ogthumb') || '';
                    var favicon = card.getAttribute('data-favicon') || '';
                    var imageHtml = '';
                    var placeholder = card.getAttribute('data-placeholder') || '';
                    if(photo) {
                        imageHtml = '<img src="'+photo+'" alt="Photo"/>';
                    } else if(youtube) {
                        var match = youtube.match(/v=([^&]+)/);
                        var ytid = match ? match[1] : null;
                        if(ytid) imageHtml = '<iframe src="https://www.youtube.com/embed/'+ytid+'" title="YouTube video" allowfullscreen style="width:100%;height:340px;border:none;"></iframe>';
                    } else if(website) {
                        imageHtml = '<img src="'+(ogthumb || favicon)+'" alt="Website Thumbnail"/>';
                    } else {
                        imageHtml = '<img src="'+placeholder+'" alt="No Image"/>';
                    }
                    document.getElementById('postModalLabel').textContent = title;
                    document.getElementById('postModalType').textContent = type;
                    document.getElementById('postModalDesc').textContent = desc;
                    document.getElementById('postModalImageWrap').innerHTML = imageHtml;
                    var linksHtml = '';
                    if(website) linksHtml += '<a href="'+website+'" target="_blank" class="btn btn-primary">Visit Website</a>';
                    if(youtube) linksHtml += '<a href="'+youtube+'" target="_blank" class="btn btn-danger">Watch Video</a>';
                    document.getElementById('postModalLinks').innerHTML = linksHtml;
                    postModal.show();
                });
            });

            // Carousel logic for each section
            document.querySelectorAll('.news-carousel').forEach(function(carousel, idx) {
                var wrap = carousel.closest('.news-carousel-wrap');
                var leftBtn = wrap.querySelector('.carousel-btn.left');
                var rightBtn = wrap.querySelector('.carousel-btn.right');
                var dotsWrap = document.getElementById('carousel-dots-' + idx);
                var cards = carousel.querySelectorAll('.carousel-card');
                var visibleCards = 3;
                var cardWidth = 0;
                var gap = 0;
                var total = cards.length;
                var pos = 0;

                function recalcCardWidth() {
                    if(window.innerWidth < 992) {
                        cardWidth = carousel.querySelector('.carousel-card')?.offsetWidth || 320;
                        visibleCards = 1;
                        gap = 0;
                    } else {
                        cardWidth = carousel.querySelector('.carousel-card')?.offsetWidth || 320;
                        visibleCards = 3;
                        // Get computed gap between cards
                        if(cards.length > 1) {
                            var style = window.getComputedStyle(cards[1]);
                            gap = parseFloat(style.marginLeft || 0);
                        } else {
                            gap = 0;
                        }
                    }
                }

                function getDotCount() {
                    return Math.max(1, total - visibleCards + 1);
                }

                function renderDots() {
                    var dotCount = getDotCount();
                    dotsWrap.innerHTML = '';
                    for(let i=0;i<dotCount;i++) {
                        var dot = document.createElement('span');
                        dot.className = 'carousel-dot' + (i===pos?' active':'');
                        dot.setAttribute('tabindex', '0');
                        dot.setAttribute('aria-label', 'Go to slide ' + (i+1));
                        dot.addEventListener('click', function(){ scrollToIdx(i); });
                        dot.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' '){ scrollToIdx(i); }});
                        dotsWrap.appendChild(dot);
                    }
                }

                function updateDots() {
                    var dots = dotsWrap.querySelectorAll('.carousel-dot');
                    dots.forEach(function(dot, i){ dot.classList.toggle('active', i===pos); });
                }
                function scrollToIdx(idx) {
                    pos = idx;
                    var scrollAmount = (cardWidth + gap) * pos;
                    carousel.scrollTo({ left: scrollAmount, behavior: 'smooth' });
                    updateDots();
                    updateBtns();
                }
                function updateBtns() {
                    var dotCount = getDotCount();
                    leftBtn.disabled = pos === 0;
                    rightBtn.disabled = pos >= dotCount-1;
                }
                leftBtn.addEventListener('click', function(){
                    if(pos>0) scrollToIdx(pos-1);
                });
                rightBtn.addEventListener('click', function(){
                    if(pos<getDotCount()-1) scrollToIdx(pos+1);
                });
                // Responsive: recalc on resize
                window.addEventListener('resize', function(){
                    recalcCardWidth();
                    renderDots();
                    updateDots();
                    updateBtns();
                    scrollToIdx(pos);
                });
                // Init
                recalcCardWidth();
                renderDots();
                scrollToIdx(0);
            });
        });
        </script>

@include('footer')
</body>
</html>
