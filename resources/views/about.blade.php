<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About - Vision & Mission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>
<body>
    @include('navbar')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="bg-white rounded-4 shadow p-5 mb-5" style="border: 2.5px solid #d81b60; background: #fff6fa;">
                    <h2 class="fw-bold text-center mb-4" style="color:#d81b60; font-size:2.5rem; letter-spacing:1px;">Vision & Mission</h2>
                    <!-- Tabs Nav -->
                    <ul class="nav nav-tabs justify-content-center mb-4" id="aboutTabs" role="tablist" style="font-size:1.25rem;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-pink fw-bold" id="school-tab" data-bs-toggle="tab" data-bs-target="#school" type="button" role="tab" aria-controls="school" aria-selected="true" style="font-size:1.18rem;">SCHOOL</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-pink fw-bold" id="lc-tab" data-bs-toggle="tab" data-bs-target="#lc" type="button" role="tab" aria-controls="lc" aria-selected="false" style="font-size:1.18rem;">LEARNING COMMONS</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="aboutTabsContent">
                        <!-- SCHOOL Tab -->
                        <div class="tab-pane fade show active" id="school" role="tabpanel" aria-labelledby="school-tab">
                            <h4 class="fw-bold text-center text-pink mb-3" style="font-size:2rem;">SCHOOL</h4>
                            <ul class="nav nav-pills justify-content-center mb-3" id="schoolSubTabs" role="tablist" style="font-size:1.13rem;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold text-pink" id="school-vision-tab" data-bs-toggle="pill" data-bs-target="#school-vision" type="button" role="tab" aria-controls="school-vision" aria-selected="true">Vision</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold text-pink" id="school-mission-tab" data-bs-toggle="pill" data-bs-target="#school-mission" type="button" role="tab" aria-controls="school-mission" aria-selected="false">Mission</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="school-vision" role="tabpanel" aria-labelledby="school-vision-tab">
                                    <h5 class="fw-bold text-center text-pink" style="font-size:1.5rem;">Vision</h5>
                                    <p class="text-center mb-4" style="font-size:1.18rem; color:#a0003a;">We, the Ignacian Marian community, witness the loving compassion of Jesus. We open new horizons with hope of nurturing learners to be humble and globally competent leaders grounded in solidarity and committed to social renewal for the common good.</p>
                                </div>
                                <div class="tab-pane fade" id="school-mission" role="tabpanel" aria-labelledby="school-mission-tab">
                                    <h5 class="fw-bold text-center text-pink" style="font-size:1.5rem;">Mission</h5>
                                    <ul class="mb-4" style="max-width: 700px; margin: 0 auto; font-size:1.15rem; color:#a0003a;">
                                        <li>Grow deeper in discernment and interior freedom and be prophetic witnesses of faith, hope, and love in today's world;</li>
                                        <li>Continuously form Ignacian Marian leaders who witness to faith, excellence, and service in varied socio-cultural settings;</li>
                                        <li>Constantly pursue innovative programs, and educational and technological strategies to develop global citizens;</li>
                                        <li>Build up resources and capabilities to create new paradigms to address social harmony toward a dignified life;</li>
                                        <li>Expand our educational thrust for the poor.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- LEARNING COMMONS Tab -->
                        <div class="tab-pane fade" id="lc" role="tabpanel" aria-labelledby="lc-tab">
                            <h4 class="fw-bold text-center text-pink mb-3" style="font-size:2rem;">LEARNING COMMONS</h4>
                            <ul class="nav nav-pills justify-content-center mb-3" id="lcSubTabs" role="tablist" style="font-size:1.13rem;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold text-pink" id="lc-vision-tab" data-bs-toggle="pill" data-bs-target="#lc-vision" type="button" role="tab" aria-controls="lc-vision" aria-selected="true">Vision</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold text-pink" id="lc-mission-tab" data-bs-toggle="pill" data-bs-target="#lc-mission" type="button" role="tab" aria-controls="lc-mission" aria-selected="false">Mission</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="lc-vision" role="tabpanel" aria-labelledby="lc-vision-tab">
                                    <h5 class="fw-bold text-center text-pink" style="font-size:1.5rem;">Vision</h5>
                                    <p class="text-center mb-4" style="font-size:1.18rem; color:#a0003a;">Lourdes College Learning Commons, driven by the compassionate service of Jesus and the Ignacian Marian Values, committed to cultivating a center for engagement that encourages and inspires learners to become ethical and socially responsible leaders.</p>
                                </div>
                                <div class="tab-pane fade" id="lc-mission" role="tabpanel" aria-labelledby="lc-mission-tab">
                                    <h5 class="fw-bold text-center text-pink" style="font-size:1.5rem;">Mission</h5>
                                    <ul style="max-width: 700px; margin: 0 auto; font-size:1.15rem; color:#a0003a;">
                                        <li>re-affirm the library’s place as the “heart” of the academic community;</li>
                                        <li>thoughtful acquisition of research and information materials for all users in their pursuit of growth in mind and spirit;</li>
                                        <li>make available and promote the use of materials that will develop in learners global competencies and socially responsible leadership;</li>
                                        <li>pursue an information and technology rich learning environment that is consistent with and upholds Catholic and Ignacian Marian Values;</li>
                                        <li>serve the larger community by engaging in outreach and activities;</li>
                                        <li>foster a work-place where individuals are prepared to be ethical and responsible leaders;</li>
                                        <li>establish collaboration with other libraries to expand access to information sources...</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('footer');
</body>
</html>
