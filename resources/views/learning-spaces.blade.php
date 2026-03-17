<title>Learning Spaces</title>

@include('navbar')
<!-- THE CONTENT OF THIS PAGE IS MANAGED IN LIBRARY CONTENT IN ADMIN -->
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
                        <div id="learningSpaceCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($settings->images as $index => $image)
                                <button type="button" data-bs-target="#learningSpaceCarousel" data-bs-slide-to="{{ $index }}"
                                    class="{{ $index === 0 ? 'active' : '' }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner rounded shadow-sm">
                                @foreach($settings->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Learning Space Image {{ $index + 1 }}" style="height: 450px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#imgModal" data-img="{{ asset('storage/' . $image) }}">
                                </div>
                                @endforeach
                            </div>
                            @if(count($settings->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#learningSpaceCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#learningSpaceCarousel" data-bs-slide="next">
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
                            transition: transform 0.33s cubic-bezier(.4, 1.6, .6, 1), box-shadow 0.2s;
                        }

                        .learning-space-img:hover,
                        .learning-space-img:focus {
                            transform: scale(1.13) rotate(-2deg);
                            z-index: 2;
                            box-shadow: 0 8px 32px rgba(232, 62, 140, 0.18);
                        }
                    </style>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var imgModal = document.getElementById('imgModal');
                            var modalImg = document.getElementById('modalImg');

                            // Handle carousel image clicks
                            document.querySelectorAll('#learningSpaceCarousel img').forEach(function(img) {
                                img.addEventListener('click', function() {
                                    var src = this.getAttribute('data-img');
                                    modalImg.src = src;
                                });
                            });

                            // Clear image on modal close
                            imgModal.addEventListener('hidden.bs.modal', function() {
                                modalImg.src = '';
                            });
                        });
                    </script>
                    <div class="mb-4">
                        <h4 class="fw-semibold" style="color:#e83e8c;">What are Learning Spaces?</h4>
                        <p class="fs-5">{{ $settings->description }}</p>
                    </div>

                    @if($settings->content_sections && count($settings->content_sections) > 0)
                    @foreach($settings->content_sections as $section)
                    <div class="mb-4">
                        <h4 class="fw-semibold" style="color:#e83e8c;">{{ $section['heading'] }}</h4>
                        @if($section['type'] === 'list')
                        <ul class="fs-5">
                            @foreach($section['items'] as $item)
                            <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                        @elseif($section['type'] === 'numbered')
                        <ol class="fs-5">
                            @foreach($section['items'] as $item)
                            <li>{{ $item }}</li>
                            @endforeach
                        </ol>
                        @endif
                    </div>
                    @endforeach
                    @endif
                    <div class="alert alert-info mt-4" style="background:#f8bbd0; color:#e83e8c; border:1px solid #e83e8c;">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        For more information, please contact the library staff or visit our website.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer')