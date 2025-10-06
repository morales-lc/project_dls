<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Information Literacy Seminars</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('navbar')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <h2 class="fw-bold mb-4 text-center">Information Literacy Seminars</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="mb-4 text-center">
                <a href="{{ route('information_literacy.create') }}" class="btn btn-primary">Post New Seminar</a>
                <a href="{{ route('information_literacy.manage') }}" class="btn btn-outline-secondary ms-2">Manage Posts</a>
            </div>

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
                                <small class="text-muted ms-2 toggle-indicator">&#x25B6;</small>
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
            if(e.target.tagName.toLowerCase() === 'a' || e.target.closest('a')) return;
            openModalFromCard(card);
        });
        card.addEventListener('keydown', function(e){
            if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModalFromCard(card); }
        });
    });
});
</script>
</body>
</html>
