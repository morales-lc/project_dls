@include('navbar')


<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 mb-4">
                <div class="card-body">
                    <h1 class="mb-3 fw-bold text-center" style="color:#e83e8c;">Netzone</h1>
                    <hr class="mb-4" style="border-top:2px solid #e83e8c;">
                    <div class="mb-4 d-flex flex-column flex-md-row align-items-center justify-content-center gap-4">
                        <img src="{{ asset('images/netzone1.png') }}" alt="Netzone 1" class="img-fluid rounded shadow-sm learning-space-img" style="max-width: 420px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imgModal" data-img="{{ asset('images/netzone1.png') }}">
                        <img src="{{ asset('images/netzone2.png') }}" alt="Netzone 2" class="img-fluid rounded shadow-sm learning-space-img" style="max-width: 420px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imgModal" data-img="{{ asset('images/netzone2.png') }}">
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
    .reminders-card { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 6px 20px rgba(0,0,0,0.08); margin: 2rem auto; max-width:800px; transition: transform .3s ease, box-shadow .3s ease; }
    .reminders-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,0.15); }
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
                        <h4 class="fw-semibold" style="color:#e83e8c;">What is the Netzone?</h4>
                        <p class="fs-5">The College Library provides 30 terminals for the use of students. The Senior High School has its own Internet Room and has 10 terminals for use. In addition, there are spaces for students to use their laptops or notebooks.</p>
                    </div>
                    <div class="reminders-card" role="region" aria-labelledby="netzone-reminders">
                        <div id="netzone-reminders" class="reminders-title" style="font-size:1.25rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#e83e8c" viewBox="0 0 16 16" class="me-2" aria-hidden="true" focusable="false">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-11.412-1 4a.5.5 0 0 1-.97 0l-1-4A.5.5 0 0 1 6.5 0h3a.5.5 0 0 1 .43.588z"/>
                            </svg>
                            Reminders While Using the Netzone
                        </div>
                        <ul class="reminders-list mt-3" style="list-style:none; padding-left:0;">
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-pink me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">1</span>
                                <span class="flex-fill"><strong class="text-danger">BE RESPECTFUL!</strong> Always treat the computer lab equipment AND your teacher and classmates the way that you would want your belongings and yourself to be treated.</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-pink me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">2</span>
                                <span class="flex-fill text-danger">No food or drinks near the computers. <strong>NO EXCEPTIONS.</strong></span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-warning text-dark me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">3</span>
                                <span class="flex-fill">Enter the Netzone quietly and work quietly. There are other individuals who may be using the Netone. Please be respectful.</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-warning text-dark me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">4</span>
                                <span class="flex-fill">Surf safely! Only visit assigned websites. Some web links can contain viruses or malware. Others may contain inappropriate content.</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-warning text-dark me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">5</span>
                                <span class="flex-fill">Clean up your work area before you leave.</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <span class="badge rounded-pill bg-warning text-dark me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">6</span>
                                <span class="flex-fill">Do not change computer settings or backgrounds.</span>
                            </li>
                            <li class="d-flex align-items-start mb-0">
                                <span class="badge rounded-pill bg-warning text-dark me-3" style="min-width:38px; display:inline-flex; align-items:center; justify-content:center;">7</span>
                                <span class="flex-fill">For your saving and printing needs, proceed to the Concierge.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer')