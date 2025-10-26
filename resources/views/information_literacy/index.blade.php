<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Information Literacy Seminars</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('navbar')
<div style="height: 40px;"></div>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <h2 class="fw-bold mb-4 text-center">Information Literacy Seminars</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif


            @forelse($posts as $post)
            <article class="il-card card mb-5 border-0 rounded-4 shadow-sm" tabindex="0" role="button" data-id="post-{{ $post->id }}">
                <div class="row g-0 align-items-center">
                    @if($post->image)
                    <div class="col-md-6">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="img-fluid rounded-start" style="width:100%; height:360px; object-fit:cover;">
                    </div>
                    <div class="col-md-6">
                    @else
                    <div class="col-12">
                    @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h3 class="fw-bold mb-1" style="color:#1976d2;">{{ $post->title }}</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted ms-2 toggle-indicator">&#x25B6;</small>
                                    @if(Auth::check() && Auth::user()->role !== 'guest')
                                        <form method="POST" action="{{ route('bookmarks.toggle') }}" class="bookmark-toggle" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $post->id }}">
                                        <input type="hidden" name="type" value="information_literacy">
                                            <button type="submit" class="btn btn-sm bookmark-btn {{ in_array($post->id, $bookmarkedIds ?? []) ? 'bookmarked' : '' }}" title="Toggle bookmark">
                                                <i class="bi {{ in_array($post->id, $bookmarkedIds ?? []) ? 'bi-bookmark-heart-fill' : 'bi-bookmark' }}"></i>
                                            </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2 text-muted small">{{ date('F j, Y \a\t g:i A', strtotime($post->date_time)) }} &nbsp;|&nbsp; <span class="badge bg-info">{{ ucfirst($post->type) }}</span></div>
                            <div class="mb-2"><strong>Facilitator/s:</strong> {{ $post->facilitators }}</div>
                            <p class="mb-0 preview">{!! \Illuminate\Support\Str::limit(strip_tags($post->description), 220) !!}</p>
                            <div class="full-content d-none">{!! nl2br(e($post->description)) !!}</div>
                            <div class="il-data d-none"
                                 data-image="{{ $post->image ? asset('storage/' . $post->image) : '' }}"
                                 data-title="{{ e($post->title) }}"
                                 data-datetime="{{ date('F j, Y \a\t g:i A', strtotime($post->date_time)) }}"
                                 data-type="{{ ucfirst($post->type) }}"
                                 data-facilitators="{{ e($post->facilitators) }}"
                            ></div>
                        </div>
                    </div>
                </div>
            </article>
            @empty
            <div class="text-center text-muted py-5">No information literacy seminars posted yet.</div>
            @endforelse
        </div>
    </div>
</div>

<div style="height: 120px;"></div>

@include('footer')
<!-- Information modal -->
<div class="modal fade" id="informationModal" tabindex="-1" aria-labelledby="informationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="informationModalLabel">Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6" id="informationModalImageWrap" style="display:none;">
                        <img src="" alt="" id="informationModalImage" class="img-fluid rounded">
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-2 text-muted small" id="informationModalMeta"></div>
                        <div class="mb-2"><strong>Facilitator/s:</strong> <span id="informationModalFacilitators"></span></div>
                        <div id="informationModalDescription"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Hover pop-out */
.il-card {
    transition: transform .18s ease, box-shadow .18s ease;
    cursor: pointer;
}
.il-card:hover, .il-card:focus {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 18px 40px rgba(0,0,0,0.08);
    outline: none;
}
.il-card .preview { white-space: normal; }
.il-card.expanded { transform: none; box-shadow: 0 24px 60px rgba(0,0,0,0.12); }
.il-card .toggle-indicator { transition: transform .18s ease; }
.il-card.expanded .toggle-indicator { transform: rotate(180deg); }
.il-card .full { display: none; }
.il-card.expanded .full { display: block; }
.il-card.expanded .preview { display: none; }

/* make images and content align nicely on small screens */
@media (max-width: 767px) {
    .il-card .img-fluid { height: 220px; }
}

/* Bookmark button visual states */
.bookmark-btn {
    transition: background-color .18s ease, color .18s ease, border-color .18s ease;
    /* neutral / no color by default */
    background-color: transparent !important;
    color: inherit !important;
    border: 1px solid transparent !important;
    padding-left: .55rem; padding-right: .55rem;
}
.bookmark-btn.bookmarked {
    background-color: #ffd1e3 !important; /* soft pink */
    color: #d81b60 !important;
    border-color: #ffd1e3 !important;
}
.bookmark-btn.bookmarked i { color: #d81b60 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var informationModalEl = document.getElementById('informationModal');
    var informationModal = new bootstrap.Modal(informationModalEl);

    function openModalFromCard(card){
        var data = card.querySelector('.il-data');
        var content = card.querySelector('.full-content');
        var imgWrap = document.getElementById('informationModalImageWrap');
        var imgEl = document.getElementById('informationModalImage');
        var titleEl = document.getElementById('informationModalLabel');
        var metaEl = document.getElementById('informationModalMeta');
        var facEl = document.getElementById('informationModalFacilitators');
        var descEl = document.getElementById('informationModalDescription');

        titleEl.textContent = data.getAttribute('data-title') || '';
        metaEl.textContent = (data.getAttribute('data-datetime') || '') + ' | ' + (data.getAttribute('data-type') || '');
        facEl.textContent = data.getAttribute('data-facilitators') || '';
        descEl.innerHTML = content ? content.innerHTML : '';

        var imgSrc = data.getAttribute('data-image');
        if(imgSrc){
            imgEl.src = imgSrc;
            imgWrap.style.display = '';
        } else {
            imgWrap.style.display = 'none';
            imgEl.src = '';
        }

        informationModal.show();
    }

    document.querySelectorAll('.il-card').forEach(function(card){
        card.addEventListener('click', function(e){
            // do not open modal when clicking links, buttons, forms or bookmark controls
            if (e.target.closest('a, button, form, .bookmark-toggle')) return;
            openModalFromCard(card);
        });
        card.addEventListener('keydown', function(e){
            if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModalFromCard(card); }
        });
    });

    // bookmark toggle for information literacy posts (AJAX)
    document.querySelectorAll('.bookmark-toggle').forEach(function(form) {
        // prevent parent card click from opening the modal when user interacts with this form
        form.addEventListener('click', function(ev){ ev.stopPropagation(); });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = form.querySelector('button');
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            var formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                },
                body: formData
            }).then(function(res) { return res.json(); }).then(function(data) {
                if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                    // toggle icon style and button class
                    var icon = form.querySelector('i');
                    var btnEl = form.querySelector('.bookmark-btn');
                    if (icon) {
                        if (data.status === 'bookmarked') {
                            icon.className = 'bi bi-bookmark-heart-fill';
                        } else {
                            icon.className = 'bi bi-bookmark';
                        }
                    }
                    if (btnEl) {
                        if (data.status === 'bookmarked') btnEl.classList.add('bookmarked'); else btnEl.classList.remove('bookmarked');
                    }
                } else {
                    alert((data && data.message) || 'Unexpected response');
                }
            }).catch(function(err) {
                console.error(err);
                alert('Failed to update bookmark.');
            }).finally(function() {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });
    });
    
    // Ensure visual class toggles on initial load for pre-bookmarked items (safety)
    document.querySelectorAll('.bookmark-toggle').forEach(function(form){
        var btn = form.querySelector('.bookmark-btn');
        if (!btn) return;
        var icon = btn.querySelector('i');
        if (icon && icon.className.indexOf('bi-bookmark-heart-fill') !== -1) {
            btn.classList.add('bookmarked');
        }
    });
});
</script>
</body>
</html>
