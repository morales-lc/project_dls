
<title>NETZONE</title>
@include('navbar')
<!-- THE CONTENT OF THIS PAGE IS MANAGE IN LIBRARY CONTENT IN ADMIN -->
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 mb-4">
                <div class="card-body">
                    <h1 class="mb-3 fw-bold text-center" style="color:#e83e8c;">{{ $settings->title }}</h1>
                    <hr class="mb-4" style="border-top:2px solid #e83e8c;">
                    
                    @if($settings->images && count($settings->images) > 0)
                    <!-- Image Slideshow -->
                    <div class="mb-4">
                        <div id="netzoneCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($settings->images as $index => $image)
                                <button type="button" data-bs-target="#netzoneCarousel" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}" 
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner rounded shadow-sm">
                                @foreach($settings->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Netzone Image {{ $index + 1 }}" style="height: 450px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#imgModal" data-img="{{ asset('storage/' . $image) }}">
                                </div>
                                @endforeach
                            </div>
                            @if(count($settings->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#netzoneCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#netzoneCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Image Modal -->
                    <div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content bg-transparent border-0">
                                <div class="modal-body p-0 d-flex justify-content-center align-items-center">
                                    <img id="modalImg" src="" alt="Preview" class="img-fluid rounded shadow" style="max-height:80vh; background:#fff;">
                                </div>
                            </div>
                        </div>
                    </div>
    <style>
    .learning-space-img {
        transition: transform 0.33s cubic-bezier(.4,1.6,.6,1), box-shadow 0.2s;
    }
    .learning-space-img:hover, .learning-space-img:focus {
        transform: scale(1.13) rotate(-2deg);
        z-index: 2;
        box-shadow: 0 8px 32px rgba(232,62,140,0.18);
    }
    .reminders-card { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 6px 20px rgba(0,0,0,0.08); margin: 2rem auto; max-width:800px; transition: transform .3s ease, box-shadow .3s ease; }
    .reminders-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,0.15); }
    </style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var imgModal = document.getElementById('imgModal');
    var modalImg = document.getElementById('modalImg');
    
    // Handle carousel image clicks
    document.querySelectorAll('#netzoneCarousel img').forEach(function(img) {
        img.addEventListener('click', function() {
            var src = this.getAttribute('data-img');
            modalImg.src = src;
        });
    });
    
    // Clear image on modal close
    imgModal.addEventListener('hidden.bs.modal', function () {
        modalImg.src = '';
    });
});
</script>
                    <div class="mb-4">
                        <h4 class="fw-semibold" style="color:#e83e8c;">What is the Netzone?</h4>
                        <p class="fs-5">{{ $settings->description }}</p>
                    </div>
                    
                    @if($settings->reminders && count($settings->reminders) > 0)
                    <div class="reminders-card" role="region" aria-labelledby="netzone-reminders">
                        <div id="netzone-reminders" class="reminders-title" style="font-size:1.25rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#e83e8c" viewBox="0 0 16 16" class="me-2" aria-hidden="true" focusable="false">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-11.412-1 4a.5.5 0 0 1-.97 0l-1-4A.5.5 0 0 1 6.5 0h3a.5.5 0 0 1 .43.588z"/>
                            </svg>
                            Reminders While Using the Netzone
                        </div>
                        <ul class="reminders-list mt-3" style="list-style:none; padding-left:0;">
                            @foreach($settings->reminders as $index => $reminder)
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-{{ $reminder['type'] === 'danger' ? 'pink' : $reminder['type'] }} me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">{{ $index + 1 }}</span>
                                <span class="flex-fill{{ $reminder['type'] === 'danger' ? ' text-danger' : '' }}">
                                    @if(str_contains($reminder['text'], 'BE RESPECTFUL!'))
                                        <strong class="text-danger">BE RESPECTFUL!</strong> {{ str_replace('BE RESPECTFUL!', '', $reminder['text']) }}
                                    @elseif(str_contains($reminder['text'], 'NO EXCEPTIONS'))
                                        {!! str_replace('NO EXCEPTIONS.', '<strong>NO EXCEPTIONS.</strong>', $reminder['text']) !!}
                                    @else
                                        {{ $reminder['text'] }}
                                    @endif
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer')