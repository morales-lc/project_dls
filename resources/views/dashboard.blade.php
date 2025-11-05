<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Learning Commons</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/news-card.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.png') }}">
</head>

<body style="min-height: 100vh; overflow-y: auto; background-color: #f8f9fa;">

    <!-- Navbar -->
    @include('navbar')
    <!-- Content -->
    <div class="container mt-5 mb-5">

        <h2 class="fw-bold mb-4">Welcome to LC MIDES Digital Library!</h2>
        <div style="height: 10px;"></div>
        <div class="mb-5">
            <!-- Clear label so users know this searches the catalog -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-pink text-white" style="font-size:0.95rem; padding:.45rem .7rem; border-radius:.65rem;"><i class="bi bi-collection me-1"></i> Catalog Search</span>
                <span class="text-muted">Search the LC MIDES library catalog</span>
            </div>


            <form class="search-bar catalog-search d-flex flex-nowrap align-items-center gap-2 flex-wrap"
                method="GET"
                action="{{ route('catalogs.search') }}"
                style="max-width: 1600px;">
                <div class="input-group flex-grow-1" style="min-width: 250px;">

                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                        placeholder="Search the Library Catalog by keyword, title, author, ISBN, ISSN, or LCCN..." aria-label="Search the library catalog" required>
                </div>
                <button type="submit"
                    class="btn btn-pink search-catalog-btn"
                    style="
                            background-color: #e83e8c; 
                            color: white; 
                            border: none; 
                            padding: 0.55rem 1.25rem; 
                            border-radius: 8px;
                            white-space: nowrap;
                            transition: all 0.3s ease;
                            box-shadow: 0 2px 8px rgba(232, 62, 140, 0.2);
                            ">
                    <i class="bi bi-search me-1"></i>Search Catalog
                </button>
            </form>

            <style>
                @media (max-width: 575.98px) {

                    .search-box .form-select,
                    .search-box .btn {
                        width: 100%;
                    }

                    .search-box {
                        gap: .5rem;
                    }
                }
            </style>
        </div>

        <!-- Featured Resources -->




        <!-- Announcements & Library Hours Side by Side -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <section class="section-white rounded-4 shadow-sm p-4 h-100">
                    <h3 class="fw-bold mb-3 text-pink">Library Announcements</h3>
                    
                    <ul class="list-group list-group-flush">
                        @if(isset($libraryAnnouncements) && $libraryAnnouncements->count())
                        @foreach($libraryAnnouncements as $ann)
                        <li class="list-group-item bg-transparent announcement-item">{{ $ann->text }}</li>
                        @endforeach
                        @else
                        <li class="list-group-item bg-transparent text-muted">No announcements at the moment.</li>
                        @endif
                    </ul>
                </section>
            </div>
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="section-white rounded-4 shadow-sm p-4 w-100 d-flex flex-column align-items-center justify-content-center">
                    <h3 class="fw-bold mb-3">Library Hours</h3>
                    @if(isset($librarySettings) && $librarySettings->library_hours_gif)
                    <img src="{{ asset('storage/' . $librarySettings->library_hours_gif) }}" alt="Library Service Hours" class="img-fluid rounded shadow library-hours-gif" style="max-width: 340px; width: 100%; height: auto; background: #fff; border: 2px solid #ffd1e3; padding: 0.5rem; transition: transform 0.35s cubic-bezier(.4,1.6,.6,1);" loading="lazy">
                    @else
                    <img src="{{ asset('images/servicehours.gif') }}" alt="Library Service Hours" class="img-fluid rounded shadow library-hours-gif" style="max-width: 340px; width: 100%; height: auto; background: #fff; border: 2px solid #ffd1e3; padding: 0.5rem; transition: transform 0.35s cubic-bezier(.4,1.6,.6,1);" loading="lazy">
                    @endif
                </div>
            </div>
        </div>

        <!-- Slideshow Section -->
        @if(isset($slideshowImages) && $slideshowImages->count())
        <div class="mb-5">
            <div id="librarySlideshow" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500">
                <div class="carousel-indicators">
                    @foreach($slideshowImages as $index => $slide)
                    <button type="button" data-bs-target="#librarySlideshow" data-bs-slide-to="{{ $index }}" 
                        class="{{ $index === 0 ? 'active' : '' }}" 
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                        aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner rounded-3 shadow-sm" style="border: 2px solid #ffd1e3;">
                    @foreach($slideshowImages as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $slide->image_path) }}" 
                             class="d-block w-100" 
                             alt="{{ $slide->caption ?? 'Slideshow image ' . ($index + 1) }}"
                             style="height: 600px; object-fit: cover;">
                        @if($slide->caption)
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded px-3 py-2">
                            <p class="mb-0">{{ $slide->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#librarySlideshow" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#librarySlideshow" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        @endif

        @auth
        <div class="mb-5">
            <h4 class="fw-bold text-pink">Featured Resources</h4>




            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card featured-card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">SIDLAK Journal</h5>
                            <p class="card-text">Browse the Lourdes College multidisciplinary research journal — latest issues, articles, and journal details.</p>
                            <div class="mt-auto">
                                <a href="{{ route('sidlak.index') }}" class="btn btn-outline-pink btn-sm">
                                    <i class="bi bi-journal-richtext me-2"></i>View SIDLAK
                                </a>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-4">
                    <div class="card featured-card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">MIDES Repository</h5>
                            <p class="card-text">Access graduate and undergraduate theses, faculty publications, and other repository materials in the MIDES collection.</p>
                            <div class="mt-auto">
                                <a href="{{ route('mides.dashboard') }}" class="btn btn-outline-pink btn-sm">
                                    <i class="bi bi-journal-text me-2"></i>Open MIDES
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card featured-card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Alert Services</h5>
                            <p class="card-text">Important alerts and timely notices grouped by month and department — stay informed about library services and updates.</p>
                            <div class="mt-auto">
                                <a href="{{ route('alert-services.index') }}" class="btn btn-outline-pink btn-sm">
                                    <i class="bi bi-bell me-2"></i>View Alerts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endauth


        <!-- Posts by Type -->

        @php
        $types = ['Announcement', 'Event', 'Update', 'Post'];
        @endphp
        @foreach($types as $i => $type)
        <section class="{{ $i % 2 == 0 ? 'section-white' : 'section-pink' }} rounded-4 shadow-sm p-4 mb-5 position-relative" style="border:2.5px solid #4a90e2;">
            <h3 class="fw-bold mb-3 {{ $i % 2 == 0 ? 'text-pink' : 'text-white' }}">{{ $type == 'Post' ? 'Latest Posts' : $type . 's' }}</h3>
            <div class="news-carousel-wrap">
                @php
                $cardCount = isset($posts) ? $posts->where('type', $type)->count() : 0;
                @endphp
                <button class="carousel-btn modern-btn left" type="button" aria-label="Scroll left" tabindex="0">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="14" cy="14" r="13" stroke="#d81b60" stroke-width="2.5" fill="#fff" />
                        <path d="M16.5 9L12 14L16.5 19" stroke="#d81b60" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
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
                            data-placeholder="{{ asset('images/placeholder.jpg') }}">
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
                                @if(Auth::check() && Auth::user()->role !== 'guest')
                                @php
                                // Prefer controller-provided map of bookmarked post IDs to avoid per-item DB queries.
                                $postBookmarked = false;
                                if (isset($bookmarkedPostIds) && is_array($bookmarkedPostIds)) {
                                $postBookmarked = in_array($post->id, $bookmarkedPostIds);
                                } else {
                                $sf = Auth::user()->studentFaculty ?? null;
                                if ($sf) {
                                $postBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                                ->where('bookmarkable_type', \App\Models\Post::class)
                                ->where('bookmarkable_id', $post->id)
                                ->exists();
                                }
                                }
                                @endphp
                                <form action="{{ route('bookmarks.toggle') }}" method="POST" class="d-inline ms-2 post-bookmark-toggle" style="margin-top:8px;" onsubmit="event.stopPropagation();">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $post->id }}">
                                    <input type="hidden" name="type" value="post">
                                    <button type="submit" class="btn btn-sm {{ $postBookmarked ? 'btn-primary' : 'btn-outline-secondary' }} post-bookmark-btn">
                                        <i class="bi {{ $postBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                                        <span>{{ $postBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="carousel-card w-100 text-center text-muted" style="min-width:220px;">No {{ $type == 'Post' ? 'posts' : strtolower($type) . 's' }} yet.</div>
                    @endif
                </div>
                <button class="carousel-btn modern-btn right" type="button" aria-label="Scroll right" tabindex="0">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="14" cy="14" r="13" stroke="#d81b60" stroke-width="2.5" fill="#fff" />
                        <path d="M11.5 9L16 14L11.5 19" stroke="#d81b60" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <div class="carousel-dots mt-3 text-center" id="carousel-dots-{{ $i }}"></div>
        </section>
        @endforeach
        <!-- Post view Modal -->
        @include('partials.post-modal')

        <!-- Library Services -->
        <div class="mb-5">
            <h4 class="fw-bold">Library Services</h4>
            <ul class="list-group">
                <li class="list-group-item">Catalog Browsing</li>
                <li class="list-group-item">Book Borrowing</li>
                <li class="list-group-item">Alert Services</li>
                <li class="list-group-item">ALINET (Appointment Scheduling)</li>
                <li class="list-group-item">Information Literacy</li>
                <li class="list-group-item">Scanning Services</li>
                <li class="list-group-item">Learning Spaces</li>
            </ul>
        </div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    @include('footer')
</body>

</html>