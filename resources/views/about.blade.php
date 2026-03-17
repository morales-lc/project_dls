<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About - Vision & Mission</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #fdf3f7;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
    }

    .content-wrapper {
      flex: 1;
    }

    .text-pink {
      color: #d81b60 !important;
    }

    .section-card {
      border: 2.5px solid #d81b60;
      background: linear-gradient(180deg, #fff6fa 0%, #ffffff 100%);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(216, 27, 96, 0.15);
      transition: all 0.3s ease;
    }

    .section-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(216, 27, 96, 0.25);
    }

    h2.section-title {
      color: #d81b60;
      font-size: 2.5rem;
      letter-spacing: 1px;
      text-align: center;
      font-weight: 700;
    }

    .nav-tabs {
      border-bottom: none;
    }

    .nav-tabs .nav-link.active {
      background-color: #d81b60 !important;
      color: white !important;
      border: 2px solid #d81b60 !important;
      border-radius: 30px;
      padding: 0.6rem 1.5rem;
      font-weight: 700;
    }

    .nav-tabs .nav-link {
      color: #d81b60 !important;
      background-color: white;
      border: 2px solid #d81b60;
      border-radius: 30px;
      margin: 0 0.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
      background-color: #d81b60;
      color: white !important;
    }

    .nav-pills .nav-link {
      border-radius: 50px;
      border: 2px solid #d81b60;
      color: #d81b60 !important;
      margin: 0 0.4rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
      background-color: #d81b60 !important;
      color: white !important;
    }

    p, li {
      color: #6c0030;
      line-height: 1.7;
    }

    ul {
      list-style-type: disc;
      padding-left: 1.5rem;
    }

    .carousel-item img {
      height: 500px;
      object-fit: cover;
      filter: brightness(0.95);
      transition: transform 0.5s ease;
    }

    .carousel-item:hover img {
      transform: scale(1.03);
    }

    .carousel-caption {
      bottom: 2rem;
    }

    .carousel-caption h5 {
      color: #fff;
      font-weight: 600;
    }

    .card-header h3 {
      font-size: 1.75rem;
      font-weight: 700;
    }

    @media (max-width: 768px) {
      h2.section-title {
        font-size: 2rem;
      }

      .nav-tabs .nav-link, .nav-pills .nav-link {
        font-size: 1rem;
        padding: 0.5rem 1rem;
      }

      .section-card {
        padding: 2rem !important;
      }

      .carousel-item img {
        height: 300px;
      }
    }
  </style>
</head>

<body>
  @include('navbar')

  <div class="content-wrapper">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
          <div class="section-card p-5 mb-5">
            <h2 class="section-title mb-4">Vision & Mission</h2>

            <!-- Tabs Nav -->
            <ul class="nav nav-tabs justify-content-center mb-4" id="aboutTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="school-tab" data-bs-toggle="tab" data-bs-target="#school" type="button" role="tab" aria-controls="school" aria-selected="true">SCHOOL</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="lc-tab" data-bs-toggle="tab" data-bs-target="#lc" type="button" role="tab" aria-controls="lc" aria-selected="false">LEARNING COMMONS</button>
              </li>
            </ul>

            <div class="tab-content" id="aboutTabsContent">
              <!-- SCHOOL Tab -->
              <div class="tab-pane fade show active" id="school" role="tabpanel" aria-labelledby="school-tab">
                <h4 class="fw-bold text-center text-pink mb-3 fs-2">SCHOOL</h4>

                <ul class="nav nav-pills justify-content-center mb-3" id="schoolSubTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="school-vision-tab" data-bs-toggle="pill" data-bs-target="#school-vision" type="button" role="tab">Vision</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="school-mission-tab" data-bs-toggle="pill" data-bs-target="#school-mission" type="button" role="tab">Mission</button>
                  </li>
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade show active" id="school-vision" role="tabpanel">
                    <h5 class="fw-bold text-center text-pink fs-4">Vision</h5>
                    <p class="text-center mb-4 fs-5">
                      We, the Ignacian Marian community, witness the loving compassion of Jesus. We open new horizons with hope of nurturing learners to be humble and globally competent leaders grounded in solidarity and committed to social renewal for the common good.
                    </p>
                  </div>

                  <div class="tab-pane fade" id="school-mission" role="tabpanel">
                    <h5 class="fw-bold text-center text-pink fs-4">Mission</h5>
                    <ul class="mb-4 fs-5" style="max-width: 700px; margin: 0 auto;">
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
                <h4 class="fw-bold text-center text-pink mb-3 fs-2">LEARNING COMMONS</h4>

                <ul class="nav nav-pills justify-content-center mb-3" id="lcSubTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="lc-vision-tab" data-bs-toggle="pill" data-bs-target="#lc-vision" type="button" role="tab">Vision</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lc-mission-tab" data-bs-toggle="pill" data-bs-target="#lc-mission" type="button" role="tab">Mission</button>
                  </li>
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade show active" id="lc-vision" role="tabpanel">
                    <h5 class="fw-bold text-center text-pink fs-4">Vision</h5>
                    <p class="text-center mb-4 fs-5">
                      Lourdes College Learning Commons, driven by the compassionate service of Jesus and the Ignacian Marian Values, committed to cultivating a center for engagement that encourages and inspires learners to become ethical and socially responsible leaders.
                    </p>
                  </div>

                  <div class="tab-pane fade" id="lc-mission" role="tabpanel">
                    <h5 class="fw-bold text-center text-pink fs-4">Mission</h5>
                    <ul class="fs-5" style="max-width: 700px; margin: 0 auto;">
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

      <!-- Slideshow Section -->
      @if(isset($slideshowImages) && $slideshowImages->count())
      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="border: 3px solid #d81b60;">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #d81b60 0%, #e91e63 100%);">
              <h3 class="mb-0 fw-bold"><i class="bi bi-images me-2"></i>Library Gallery</h3>
            </div>
            <div class="card-body p-0">
              <div id="aboutSlideshow" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-indicators">
                  @foreach($slideshowImages as $index => $slide)
                    <button type="button" data-bs-target="#aboutSlideshow" data-bs-slide-to="{{ $index }}"
                      class="{{ $index === 0 ? 'active' : '' }}"
                      aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                      aria-label="Slide {{ $index + 1 }}"
                      style="background-color: #d81b60;"></button>
                  @endforeach
                </div>
                <div class="carousel-inner">
                  @foreach($slideshowImages as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                      <img src="{{ asset('storage/' . $slide->image_path) }}" class="d-block w-100"
                        alt="{{ $slide->caption ?? 'Library image ' . ($index + 1) }}">
                      @if($slide->caption)
                      <div class="carousel-caption d-none d-md-block">
                        <div class="bg-dark bg-opacity-75 rounded px-4 py-3 d-inline-block">
                          <h5 class="mb-0 fw-bold">{{ $slide->caption }}</h5>
                        </div>
                      </div>
                      @endif
                    </div>
                  @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#aboutSlideshow" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#aboutSlideshow" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  @include('footer')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
