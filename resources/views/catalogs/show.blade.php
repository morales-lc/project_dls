@include('navbar')
<title>{{ $catalog->title }}</title>
<link href="{{ asset('css/catalog-show.css') }}" rel="stylesheet">


<div class="container catalog-page">
    <div class="catalog-container">
        <div class="back-btn">
            <button onclick="window.history.back()" class="btn btn-outline-secondary btn-sm btn-animated">
                <i class="bi bi-arrow-left"></i> Back to Search
            </button>
        </div>

        <div class="catalog-cover">
            <img src="{{ $catalog->cover_image ? asset('storage/' . $catalog->cover_image) : asset('images/book-placeholder.png') }}"
                alt="Book Cover">
        </div>

        <div class="catalog-details">
            <div class="top-line">
                <div class="title-block">
                    <h4 class="title-text" title="{{ $catalog->title }}">{{ $catalog->title }}</h4>
                </div>
                <div class="button-group">
                    @if(!Auth::check() || Auth::user()->role !== 'guest')
                    <div class="button-group request-buttons">
                        <a href="{{ route('lira.form', [
                                            'catalog_id' => $catalog->id,
                                            'title' => $catalog->title,
                                            'author' => $catalog->author,
                                            'call_number' => $catalog->call_number,
                                            'isbn' => $catalog->isbn,
                                            'lccn' => $catalog->lccn,
                                            'issn' => $catalog->issn,
                                            'action' => 'borrow'
                                        ]) }}" class="btn btn-pink btn-sm btn-animated">
                            <i class="bi bi-send"></i> Request Borrow
                        </a>

                        <a href="{{ route('lira.form', [
                                            'catalog_id' => $catalog->id,
                                            'title' => $catalog->title,
                                            'author' => $catalog->author,
                                            'call_number' => $catalog->call_number,
                                            'isbn' => $catalog->isbn,
                                            'lccn' => $catalog->lccn,
                                            'issn' => $catalog->issn,
                                            'action' => 'scanning'
                                        ]) }}" class="btn btn-outline-pink btn-sm btn-animated">
                            <i class="bi bi-printer"></i> Request Scanning
                        </a>
                    </div>
                    @endif

                    @if(Auth::check() && Auth::user()->role !== 'guest')
                    @php $isBookmarked = $catalogBookmarked ?? false; @endphp
                    <form method="POST" action="{{ route('bookmarks.toggle') }}"
                        class="d-inline bookmark-toggle mt-2 w-100 text-center">
                        @csrf
                        <input type="hidden" name="id" value="{{ $catalog->id }}">
                        <input type="hidden" name="type" value="catalog">
                        <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-primary' : 'btn-outline-dark' }} btn-animated w-100 bookmark-btn">
                            <i class="bi {{ $isBookmarked ? 'bi-bookmark-fill' : 'bi-plus-circle' }} me-1"></i>
                            <span class="bookmark-text">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                        </button>
                    </form>

                    @php $isInCart = $catalogInCart ?? false; @endphp
                    <form method="POST" action="{{ route('cart.toggle') }}"
                        class="d-inline cart-toggle mt-2 w-100 text-center">
                        @csrf
                        <input type="hidden" name="id" value="{{ $catalog->id }}">
                        <input type="hidden" name="type" value="catalog">
                        <button type="submit" class="btn btn-sm {{ $isInCart ? 'btn-success' : 'btn-outline-primary' }} btn-animated w-100 cart-btn">
                            <i class="bi {{ $isInCart ? 'bi-cart-check-fill' : 'bi-cart-plus' }} me-1"></i>
                            <span class="cart-text">{{ $isInCart ? 'In My Cart' : 'Add to My Cart' }}</span>
                        </button>
                    </form>
                    @elseif(!Auth::check())
                    <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm btn-animated mt-2 w-100 text-center">
                        <i class="bi bi-plus-circle"></i> Add to list
                    </a>
                    @endif
                </div>
            </div>

            <div class="section-title">Catalog Information</div>
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Title</th>
                            <td>{{ $catalog->title }}</td>
                        </tr>
                        <tr>
                            <th>Author</th>
                            <td>{{ $catalog->author ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Call Number</th>
                            <td>{{ $catalog->call_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Sub Location</th>
                            <td>{{ $catalog->sublocation ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Publisher</th>
                            <td>{{ $catalog->publisher ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Year</th>
                            <td>{{ $catalog->year ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Edition</th>
                            <td>{{ $catalog->edition ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Format</th>
                            <td>{{ $catalog->format ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Content Type</th>
                            <td>{{ $catalog->content_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Media Type</th>
                            <td>{{ $catalog->media_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Carrier Type</th>
                            <td>{{ $catalog->carrier_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Copies Count</th>
                            <td>
                                @if(is_null($catalog->copies_count))
                                    <span class="text-warning fw-semibold">No copy information available</span>
                                @else
                                    {{ $catalog->copies_count }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Borrowed Count</th>
                            <td>
                                @if(is_null($catalog->copies_count))
                                    <span class="text-warning fw-semibold">No copy information available</span>
                                @else
                                    {{ $catalog->borrowed_count ?? 0 }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Available Copies</th>
                            <td>
                                @if(is_null($catalog->copies_count))
                                    <span class="text-warning fw-semibold">No copy information available</span>
                                @else
                                    {{ max(((int) $catalog->copies_count) - ((int) ($catalog->borrowed_count ?? 0)), 0) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>ISBN</th>
                            <td>{{ $catalog->isbn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>ISSN</th>
                            <td>{{ $catalog->issn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>LCCN</th>
                            <td>{{ $catalog->lccn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Subjects</th>
                            <td>{{ $catalog->subjects ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Additional Details</th>
                            <td style="white-space: pre-line;">{!! nl2br(e($catalog->additional_details ?? '-')) !!}</td>
                        </tr>
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
            carousel.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        rightBtn.addEventListener('click', () => {
            carousel.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
    });
</script>


@include('footer')

<script>
    // Delegated submit listener so it always intercepts form submission (prevents full page reload)
    (function(){
        async function handleToggle(e){
            e.preventDefault();
            var form = e.target.closest('.bookmark-toggle');
            if (!form) return;
            var btn = form.querySelector('.bookmark-btn');
            if (!btn) return;

            var originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>...';

            try {
                var fd = new FormData(form);
                var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]').value;
                var resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: fd
                });
                var data = await resp.json();

                if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                    var isNowBookmarked = data.status === 'bookmarked';
                    if (isNowBookmarked) {
                        btn.classList.remove('btn-outline-dark');
                        btn.classList.add('btn-primary');
                        btn.querySelector('i')?.classList.remove('bi-plus-circle');
                        btn.querySelector('i')?.classList.add('bi-bookmark-fill');
                        btn.querySelector('.bookmark-text')?.textContent = 'Bookmarked';
                    } else {
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-outline-dark');
                        btn.querySelector('i')?.classList.remove('bi-bookmark-fill');
                        btn.querySelector('i')?.classList.add('bi-plus-circle');
                        btn.querySelector('.bookmark-text')?.textContent = 'Add to list';
                    }

                    // pop animation
                    btn.classList.add('bookmark-pop');
                    var handle = function() {
                        btn.classList.remove('bookmark-pop');
                        btn.removeEventListener('animationend', handle);
                    };
                    btn.addEventListener('animationend', handle);

                    // toast-like alert
                    var alertEl = document.createElement('div');
                    alertEl.className = 'alert alert-success position-fixed end-0 m-4 shadow-sm';
                    alertEl.style.zIndex = 1050;
                    alertEl.textContent = data.message || (isNowBookmarked ? 'Added to your list' : 'Removed from your list');
                    document.body.appendChild(alertEl);
                    setTimeout(function(){ alertEl.remove(); }, 2200);
                } else {
                    alert((data && data.message) || 'Unexpected response');
                }
            } catch (err) {
                console.error(err);
                alert('Failed to toggle bookmark.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        document.addEventListener('submit', function(e){
            if (e.target && e.target.classList && e.target.classList.contains('bookmark-toggle')){
                handleToggle(e);
            }
        });
    })();
</script>

<script>
    (function(){
        async function handleCartToggle(e){
            e.preventDefault();
            var form = e.target.closest('.cart-toggle');
            if (!form) return;
            var btn = form.querySelector('.cart-btn');
            if (!btn) return;

            btn.disabled = true;
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>...';

            try {
                var fd = new FormData(form);
                var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]').value;
                var resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: fd
                });
                var data = await resp.json();

                if (data && (data.status === 'added' || data.status === 'removed')) {
                    var inCart = data.status === 'added';
                    btn.classList.toggle('btn-success', inCart);
                    btn.classList.toggle('btn-outline-primary', !inCart);
                    btn.innerHTML = inCart
                        ? '<i class="bi bi-cart-check-fill me-1"></i><span class="cart-text">In My Cart</span>'
                        : '<i class="bi bi-cart-plus me-1"></i><span class="cart-text">Add to My Cart</span>';
                } else {
                    alert((data && data.message) || 'Unexpected response');
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                console.error(err);
                alert('Failed to update cart.');
                btn.innerHTML = originalHtml;
            } finally {
                btn.disabled = false;
            }
        }

        document.addEventListener('submit', function(e){
            if (e.target && e.target.classList && e.target.classList.contains('cart-toggle')) {
                handleCartToggle(e);
            }
        });
    })();
</script>