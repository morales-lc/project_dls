@include('navbar')


<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 mb-4">
                <div class="card-body">
                    <h1 class="mb-3 fw-bold text-center" style="color:#e83e8c;">Learning Spaces</h1>
                    <hr class="mb-4" style="border-top:2px solid #e83e8c;">
                    <div class="mb-4 d-flex flex-column flex-md-row align-items-center justify-content-center gap-4">
                        <img src="{{ asset('images/IMG_1462.JPG') }}" alt="Room 3 (Theses Files)" class="img-fluid rounded shadow-sm learning-space-img" style="max-width: 420px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imgModal" data-img="{{ asset('images/IMG_1462.JPG') }}">
                        <img src="{{ asset('images/10.png') }}" alt="Library Overview" class="img-fluid rounded shadow-sm learning-space-img" style="max-width: 420px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imgModal" data-img="{{ asset('images/10.png') }}">
                    </div>

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var imgModal = document.getElementById('imgModal');
    var modalImg = document.getElementById('modalImg');
    document.querySelectorAll('.learning-space-img').forEach(function(img) {
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
                        <h4 class="fw-semibold" style="color:#e83e8c;">What are Learning Spaces?</h4>
                        <p class="fs-5">Learning spaces are areas in the library designed to support different learning activities, from individual study to group collaboration. These spaces are equipped with resources and facilities to enhance the learning experience of students and faculty.</p>
                    </div>
                    <div class="mb-4">
                        <h4 class="fw-semibold" style="color:#e83e8c;">Types of Learning Spaces</h4>
                        <ul class="fs-5">
                            <li><strong>Individual Study Areas:</strong> Quiet zones for focused, independent work.</li>
                            <li><strong>Group Study Rooms:</strong> Spaces for collaborative projects and discussions.</li>
                            <li><strong>Technology Zones:</strong> Areas equipped with computers and multimedia tools.</li>
                            <li><strong>Flexible Seating:</strong> Comfortable seating arrangements for reading and relaxation.</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="fw-semibold" style="color:#e83e8c;">How to Reserve a Space</h4>
                        <ol class="fs-5">
                            <li>Visit the library's main desk or use the online reservation system.</li>
                            <li>Choose the type of space and time slot you need.</li>
                            <li>Provide your student or faculty ID for verification.</li>
                            <li>Enjoy your reserved learning space!</li>
                        </ol>
                    </div>
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

