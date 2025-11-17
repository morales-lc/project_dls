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
    
    <!-- Profile Completion Reminder -->
    @include('partials.profile-reminder')
    
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

        <!-- Latest Alert Books -->
        @if(isset($latestAlertBooks) && $latestAlertBooks->count())
        <section class="section-white rounded-4 shadow-sm p-4 mb-5 position-relative" style="border:2.5px solid #4a90e2;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-pink mb-0">Latest Alert Books</h3>
                <a href="{{ route('alert-services.index') }}" class="btn btn-outline-pink btn-sm">
                    <i class="bi bi-collection me-1"></i>View All
                </a>
            </div>
            <div class="news-carousel-wrap">
                <button class="carousel-btn modern-btn left" type="button" aria-label="Scroll left" tabindex="0">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 21L10.5 14L17.5 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="news-carousel" id="alert-books-carousel">
                    @foreach($latestAlertBooks as $book)
                    <div class="carousel-card alert-book-card" data-pdf-url="{{ $book->pdf_path ? asset('storage/'.$book->pdf_path) : '' }}">
                        <div class="alert-book-cover">
                            @if($book->cover_image)
                            <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title ?? 'Book Cover' }}" loading="lazy">
                            @else
                            <div class="placeholder-cover">
                                <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                            @endif
                        </div>
                        <div class="alert-book-overlay">
                            <div class="alert-book-title">{{ $book->title ?? 'Untitled' }}</div>
                            @if($book->author)
                            <div class="alert-book-author">{{ $book->author }}</div>
                            @endif
                            <div class="alert-book-meta">
                                {{ DateTime::createFromFormat('!m', $book->month)->format('F') }} {{ $book->year }}
                            </div>
                            @auth
                            @if(Auth::user()->role !== 'guest')
                            <div class="d-flex flex-column gap-2 mt-3">
                                <form action="{{ route('bookmarks.toggle') }}" method="POST" class="bookmark-toggle-alert-dashboard" onclick="event.stopPropagation();">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $book->id }}">
                                    <input type="hidden" name="type" value="alert_book">
                                    <button type="submit" class="btn btn-sm w-100 {{ in_array($book->id, $bookmarkedAlertBookIds ?? []) ? 'btn-success' : 'btn-outline-light' }}">
                                        <i class="bi {{ in_array($book->id, $bookmarkedAlertBookIds ?? []) ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                        <span class="label">{{ in_array($book->id, $bookmarkedAlertBookIds ?? []) ? 'Bookmarked' : 'Bookmark' }}</span>
                                    </button>
                                </form>
                                <a href="{{ route('lira.jotform', ['title' => $book->title, 'author' => $book->author, 'call_number' => $book->call_number]) }}"
                                    class="btn btn-pink btn-sm" onclick="event.stopPropagation();">
                                    <i class="bi bi-journal-bookmark-fill"></i> Request via LiRA
                                </a>
                            </div>
                            @endif
                            @else
                            <div class="d-flex flex-column gap-2 mt-3">
                                <a href="{{ route('login') }}" onclick="event.stopPropagation();" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-box-arrow-in-right"></i> Log in to bookmark
                                </a>
                            </div>
                            @endauth
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-btn modern-btn right" type="button" aria-label="Scroll right" tabindex="0">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.5 7L17.5 14L10.5 21" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="carousel-dots mt-3 text-center" id="alert-books-carousel-dots"></div>
        </section>

        <style>
            /* Alert Book Card Styles */
            .alert-book-card {
                position: relative;
                min-width: 220px;
                max-width: 220px;
                border-radius: 1.2rem;
                overflow: hidden;
                box-shadow: 0 8px 32px rgba(40, 40, 60, 0.18);
                transition: transform 0.25s ease, box-shadow 0.25s ease;
                cursor: pointer;
                background: #fff;
            }

            .alert-book-card:hover {
                transform: scale(1.05);
                box-shadow: 0 12px 36px rgba(216, 27, 96, 0.25);
            }

            .alert-book-cover {
                width: 100%;
                aspect-ratio: 3 / 4.3;
                overflow: hidden;
                position: relative;
            }

            .alert-book-cover img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .placeholder-cover {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #f5f7fa 0%, #e3e8ef 100%);
            }

            .alert-book-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.05));
                color: #fff;
                opacity: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-end;
                padding: 1.2rem;
                transition: opacity 0.3s ease;
            }

            .alert-book-card:hover .alert-book-overlay {
                opacity: 1;
            }

            .alert-book-title {
                font-weight: 600;
                text-align: center;
                font-size: 0.95rem;
                margin-bottom: 0.4rem;
                line-height: 1.2em;
                display: -webkit-box;
                line-clamp: 2;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .alert-book-author {
                font-size: 0.8rem;
                text-align: center;
                margin-bottom: 0.3rem;
                opacity: 0.9;
                font-style: italic;
            }

            .alert-book-meta {
                font-size: 0.75rem;
                text-align: center;
                opacity: 0.8;
                margin-bottom: 0.5rem;
            }

            @media (max-width: 600px) {
                .alert-book-card {
                    min-width: 180px;
                    max-width: 180px;
                }
            }
        </style>
        @endif


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

    <script>
        // Handle alert book card clicks to view PDF
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert-book-card').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    // Don't open PDF if clicking on buttons or forms
                    if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) {
                        return;
                    }
                    var pdfUrl = this.getAttribute('data-pdf-url');
                    if (pdfUrl) {
                        window.open(pdfUrl, '_blank');
                    } else {
                        alert('No PDF available for this book.');
                    }
                });
            });

            // Handle bookmark toggle for alert books
            document.querySelectorAll('.bookmark-toggle-alert-dashboard').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var btn = form.querySelector('button');
                    if (!btn) return;
                    var label = btn.querySelector('.label');
                    var icon = btn.querySelector('i');
                    var originalHTML = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing';

                    var formData = new FormData(form);
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    var csrfValue = csrfToken ? csrfToken.getAttribute('content') : '';
                    
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfValue
                        },
                        body: formData
                    }).then(function(res) {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    }).then(function(data) {
                        if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                            var bookmarked = data.status === 'bookmarked';
                            var iconClass = bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark';
                            var labelText = bookmarked ? 'Bookmarked' : 'Bookmark';
                            var btnClass = bookmarked ? 'btn-success' : 'btn-outline-light';
                            
                            btn.className = 'btn btn-sm w-100 ' + btnClass;
                            btn.innerHTML = '<i class="bi ' + iconClass + '"></i> <span class="label">' + labelText + '</span>';
                            btn.disabled = false;
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = originalHTML;
                        }
                    }).catch(function(err) {
                        console.error('Error:', err);
                        alert(err && err.message ? err.message : 'Failed to update bookmark.');
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    });
                });
            });

            // Initialize alert books carousel manually
            var alertCarousel = document.getElementById('alert-books-carousel');
            if (alertCarousel) {
                var wrap = alertCarousel.closest('.news-carousel-wrap');
                var leftBtn = wrap.querySelector('.carousel-btn.left');
                var rightBtn = wrap.querySelector('.carousel-btn.right');
                var dotsWrap = document.getElementById('alert-books-carousel-dots');
                var cards = alertCarousel.querySelectorAll('.carousel-card');
                var visibleCards = 3;
                var cardWidth = 0;
                var gap = 0;
                var total = cards.length;
                var pos = 0;

                function recalcCardWidth() {
                    if (window.innerWidth < 992) {
                        cardWidth = alertCarousel.querySelector('.carousel-card')?.offsetWidth || 220;
                        visibleCards = 1;
                        gap = 0;
                    } else {
                        cardWidth = alertCarousel.querySelector('.carousel-card')?.offsetWidth || 220;
                        visibleCards = 3;
                        if (cards.length > 1) {
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
                    for (let i = 0; i < dotCount; i++) {
                        var dot = document.createElement('span');
                        dot.className = 'carousel-dot' + (i === pos ? ' active' : '');
                        dot.setAttribute('tabindex', '0');
                        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                        dot.addEventListener('click', function() {
                            scrollToIdx(i);
                        });
                        dot.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter' || e.key === ' ') {
                                scrollToIdx(i);
                            }
                        });
                        dotsWrap.appendChild(dot);
                    }
                }

                function updateDots() {
                    var dots = dotsWrap.querySelectorAll('.carousel-dot');
                    dots.forEach(function(dot, i) {
                        dot.classList.toggle('active', i === pos);
                    });
                }

                function scrollToIdx(idx) {
                    pos = idx;
                    var scrollAmount = (cardWidth + gap) * pos;
                    alertCarousel.scrollTo({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                    updateDots();
                    updateBtns();
                }

                function updateBtns() {
                    var dotCount = getDotCount();
                    leftBtn.disabled = pos === 0;
                    rightBtn.disabled = pos >= dotCount - 1;
                }
                
                leftBtn.addEventListener('click', function() {
                    if (pos > 0) scrollToIdx(pos - 1);
                });
                
                rightBtn.addEventListener('click', function() {
                    if (pos < getDotCount() - 1) scrollToIdx(pos + 1);
                });
                
                window.addEventListener('resize', function() {
                    recalcCardWidth();
                    renderDots();
                    updateDots();
                    updateBtns();
                    scrollToIdx(pos);
                });
                
                recalcCardWidth();
                renderDots();
                scrollToIdx(0);
            }
        });
    </script>

    @include('footer')
</body>

</html>