<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookmarked Items</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
      <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

  <style>
    /* Soft Pink Theme */
    .bg-pink {
      background-color: #ffd1e3 !important;
      color: #d81b60 !important;
    }

    .text-pink {
      color: #d81b60 !important;
    }

    .btn-outline-pink {
      border: 1.5px solid #ffd1e3 !important;
      color: #d81b60 !important;
      background-color: #fff !important;
      font-weight: 500;
      border-radius: 0.7rem;
      transition: 0.2s;
    }

    .btn-outline-pink:hover {
      background-color: #ffd1e3 !important;
      color: #b3134b !important;
    }

    /* Badge for type (MidesDocument, etc.) */
    .badge.bg-pink {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 600;
      border: 1px solid #ffd1e3 !important;
    }

    /* Card Header Styling */
    .card-header.bg-pink {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 700;
      border-bottom: 2px solid #ffd1e3;
    }

    /* Table header */
    thead tr {
      background: #ffd1e3 !important;
      color: #d81b60 !important;
    }

    /* Gradient background */
    .card-body {
      background: linear-gradient(180deg, #fff 90%, #ffe3ef 100%);
    }

    /* Fix text visibility everywhere */
    .text-dark, .text-pink, .badge.bg-pink, .card-header.bg-pink {
      color: #d81b60 !important;
    }
    /* Mobile specific adjustments */
    @media (max-width: 767.98px) {
      .card {
        margin: 0.4rem;
        border-radius: 1rem;
      }
      .table-responsive-mobile {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      thead { display: none; }
      tr { display: block; border-bottom: 1px solid #ffe3ef; margin-bottom: .6rem; }
      tr td { display: block; width: 100%; padding: .35rem .5rem; }
      .bookmark-badge { float: right; }
      .card-body { padding: 1rem; }
      /* Make information modal more friendly on small screens */
      .modal-dialog.modal-xl {
        max-width: 100%;
        margin: 0.5rem;
      }
    }
  </style>
</head>

<body>
  @include('navbar')

  <div class="d-flex" style="min-height: 80vh; background: #f8f9fa;">
    @include('sidebar')

  <div class="flex-grow-1 d-flex justify-content-center align-items-start py-4">
      <div class="card shadow-lg w-100 border-0" style="max-width: 1100px; border-radius: 1.5rem;">
        <div class="card-header bg-pink d-flex align-items-center" style="border-radius: 1.5rem 1.5rem 0 0;">
          <i class="bi bi-bookmark-heart-fill fs-3 me-2"></i>
          <span class="fw-bold fs-5">Bookmarked Items</span>
        </div>

        <div class="card-body">
          {{-- Tabs: All / Catalogs --}}
          <ul class="nav nav-tabs mb-3" id="bookmarkTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#tab-all" type="button" role="tab" aria-controls="tab-all" aria-selected="true">All</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="catalogs-tab" data-bs-toggle="tab" data-bs-target="#tab-catalogs" type="button" role="tab" aria-controls="tab-catalogs" aria-selected="false">Catalogs</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="alert-tab" data-bs-toggle="tab" data-bs-target="#tab-alert" type="button" role="tab" aria-controls="tab-alert" aria-selected="false">Alert Service</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="mides-tab" data-bs-toggle="tab" data-bs-target="#tab-mides" type="button" role="tab" aria-controls="tab-mides" aria-selected="false">Mides</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="sidlak-tab" data-bs-toggle="tab" data-bs-target="#tab-sidlak" type="button" role="tab" aria-controls="tab-sidlak" aria-selected="false">Sidlak</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="posts-tab" data-bs-toggle="tab" data-bs-target="#tab-posts" type="button" role="tab" aria-controls="tab-posts" aria-selected="false">Posts</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="il-tab" data-bs-toggle="tab" data-bs-target="#tab-il" type="button" role="tab" aria-controls="tab-il" aria-selected="false">Information Literacy</button>
            </li>
          </ul>

          <div class="tab-content" id="bookmarkTabsContent">
            @php
              // Prepare filtered collections for each tab
              $catalogBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable_type === \App\Models\Catalog::class);
              $alertBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable_type === \App\Models\AlertBook::class);
              $midesBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable_type === \App\Models\MidesDocument::class);
              $sidlakBookmarks = $bookmarks->filter(fn($b) => in_array($b->bookmarkable_type, [\App\Models\SidlakArticle::class, \App\Models\SidlakJournal::class]));
              $postBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable_type === \App\Models\Post::class);
              $ilBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable_type === \App\Models\InformationLiteracyPost::class);
            @endphp

            {{-- Helper to render a table for a given collection --}}
            @php
              function renderBookmarkRows($collection) {
                foreach ($collection as $bm) {
                  echo '<tr style="background: #fff;">';
                  echo '<td class="text-capitalize text-center align-middle"><span class="badge bg-pink px-3 py-2"><i class="bi bi-bookmark fs-6 me-1"></i>'.class_basename($bm->bookmarkable_type).'</span></td>';
                  echo '<td class="text-dark align-middle">';
                  if ($bm->bookmarkable) {
                    $item = $bm->bookmarkable;
                    // determine bookmark type and open route/link
                    $route = null;
                    $typeValue = 'mides';
                    if ($bm->bookmarkable_type === \App\Models\MidesDocument::class) {
                      $route = route('mides.viewer', $item->id);
                      $typeValue = 'mides';
                    } elseif ($bm->bookmarkable_type === \App\Models\AlertBook::class) {
                      $route = $item->pdf_path ? asset('storage/' . $item->pdf_path) : null;
                      $typeValue = 'alert_book';
                    } elseif ($bm->bookmarkable_type === \App\Models\SidlakArticle::class) {
                      $route = $item->pdf_file ? asset('storage/' . $item->pdf_file) : null;
                      $typeValue = 'sidlak';
                    } elseif ($bm->bookmarkable_type === \App\Models\SidlakJournal::class) {
                      $route = route('sidlak.show', $item->id);
                      $typeValue = 'sidlak_journal';
                    } elseif ($bm->bookmarkable_type === \App\Models\InformationLiteracyPost::class) {
                      $route = null;
                      $typeValue = 'information_literacy';
                    } elseif ($bm->bookmarkable_type === \App\Models\Catalog::class) {
                      $route = route('catalogs.show', $item->id);
                      $typeValue = 'catalog';
                    } elseif ($bm->bookmarkable_type === \App\Models\Post::class) {
                      $route = null;
                      $typeValue = 'post';
                    }
                    echo '<div class="fw-semibold fs-6 mb-1 text-pink">'.htmlspecialchars($item->title ?? ($item->name ?? 'Item')).'</div>';
                    echo '<div class="small text-muted">';
                    if ($bm->bookmarkable_type === \App\Models\SidlakArticle::class) {
                      echo 'Authors: '.htmlspecialchars($item->authors ?? '');
                    } elseif ($bm->bookmarkable_type === \App\Models\SidlakJournal::class) {
                      echo htmlspecialchars(($item->month ?? '').' '.($item->year ?? ''));
                    } else {
                      echo htmlspecialchars(($item->author ?? '').' '.($item->year ?? ''));
                    }
                    echo '</div>';
                  } else {
                    echo '<em class="text-danger">Item removed</em>';
                  }
                  echo '</td>';
                  echo '<td class="text-center text-dark small align-middle">'.($bm->created_at->diffForHumans()).'</td>';
                  echo '<td class="text-center align-middle">';
                  echo '<div class="d-flex justify-content-center align-items-center gap-2">';
                  if ($bm->bookmarkable) {
                    if ($bm->bookmarkable_type === \App\Models\Post::class) {
                      $post = $bm->bookmarkable;
                      echo '<button class="btn btn-sm btn-outline-pink shadow-sm px-3 open-post-from-bookmark" data-post-id="'.($post->id).'" type="button"><i class="bi bi-box-arrow-up-right"></i> Open</button>';
                    } elseif ($bm->bookmarkable_type === \App\Models\InformationLiteracyPost::class) {
                      $info = $bm->bookmarkable;
                      echo '<button class="btn btn-sm btn-outline-pink shadow-sm px-3 open-info-from-bookmark" data-info-id="'.($info->id).'" type="button"><i class="bi bi-box-arrow-up-right"></i> Open</button>';
                      // hidden il data remains in DOM for modal population
                      echo '<div class="il-bookmark-data d-none">';
                      echo '<div class="il-title">'.htmlspecialchars($info->title).'</div>';
                      echo '<div class="il-datetime">'.htmlspecialchars(date('F j, Y \a\t g:i A', strtotime($info->date_time))).'</div>';
                      echo '<div class="il-type">'.htmlspecialchars(ucfirst($info->type)).'</div>';
                      echo '<div class="il-facilitators">'.htmlspecialchars($info->facilitators).'</div>';
                      echo '<div class="il-image">'.($info->image ? asset('storage/' . $info->image) : '').'</div>';
                      echo '<div class="il-description">'.nl2br(e($info->description)).'</div>';
                      echo '</div>';
                    } elseif ($bm->bookmarkable_type === \App\Models\AlertBook::class) {
                      // For Alert Service bookmarks, provide LiRA request like in alert-services/group.blade.php
                      $lira = route('lira.jotform', [
                        'title' => $item->title ?? '',
                        'author' => $item->author ?? '',
                        'call_number' => $item->call_number ?? '',
                      ]);
                      echo '<a href="'.$lira.'" class="btn btn-sm btn-outline-pink shadow-sm px-3" target="_blank" rel="noopener noreferrer"><i class="bi bi-journal-bookmark-fill"></i> Request via LiRA</a>';
                    } else {
                      if ($route) {
                        echo '<a href="'.$route.'" class="btn btn-sm btn-outline-pink shadow-sm px-3" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open</a>';
                      }
                    }
                  }
                  // Remove form
                  echo '<form action="'.route('bookmarks.toggle').'" method="POST" class="d-inline bookmark-toggle">'.csrf_field().'<input type="hidden" name="id" value="'.($bm->bookmarkable_id).'">'.'<input type="hidden" name="type" value="'.($typeValue ?? 'mides').'">'.'<button class="btn btn-sm btn-outline-danger shadow-sm px-3" style="border-radius: 0.7rem; border: 1.5px solid #ffd1e3; font-weight: 500;"><i class="bi bi-x-circle"></i> Remove</button></form>';
                  echo '</div>';
                  echo '</td>';
                  echo '</tr>';
                }
              }
            @endphp

            {{-- All tab (full table) --}}
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="all-tab">
              @if($bookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #ffe3ef; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($bookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>

            {{-- Alert Service tab --}}
            <div class="tab-pane fade" id="tab-alert" role="tabpanel" aria-labelledby="alert-tab">
              @if($alertBookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no Alert Service bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($alertBookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>

            {{-- Catalogs tab (same table layout but filtered) --}}
            <div class="tab-pane fade" id="tab-catalogs" role="tabpanel" aria-labelledby="catalogs-tab">
              @if($catalogBookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no catalog bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($catalogBookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>

            {{-- Mides tab --}}
            <div class="tab-pane fade" id="tab-mides" role="tabpanel" aria-labelledby="mides-tab">
              @if($midesBookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #ffeef6; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no Mides bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($midesBookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>

            {{-- Sidlak (articles + journals) tab --}}
            <div class="tab-pane fade" id="tab-sidlak" role="tabpanel" aria-labelledby="sidlak-tab">
              @if($sidlakBookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no Sidlak bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($sidlakBookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>

            {{-- Posts tab --}}
            <div class="tab-pane fade" id="tab-posts" role="tabpanel" aria-labelledby="posts-tab">
              @if($postBookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no Post bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($postBookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>

            {{-- Information Literacy tab --}}
            <div class="tab-pane fade" id="tab-il" role="tabpanel" aria-labelledby="il-tab">
              @if($ilBookmarks->isEmpty())
                <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                  <i class="bi bi-bookmark-x fs-2 me-2"></i> You have no Information Literacy bookmarks yet.
                </div>
              @else
                <div class="table-responsive table-responsive-mobile">
                  <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                    <thead class="table-light">
                      <tr>
                        <th class="fw-bold text-center" style="width: 110px;">Type</th>
                        <th class="fw-bold">Title / Info</th>
                        <th class="fw-bold text-center" style="width: 120px;">Added</th>
                        <th class="fw-bold text-center" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php renderBookmarkRows($ilBookmarks); @endphp
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('partials.post-modal')

  <!-- Information Literacy Modal (for opening IL bookmarks) -->
  <div class="modal fade" id="informationModalFromBookmarks" tabindex="-1" aria-labelledby="informationModalFromBookmarksLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="informationModalFromBookmarksLabel">Title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6" id="informationModalFromBookmarksImageWrap" style="display:none;">
              <img src="" alt="" id="informationModalFromBookmarksImage" class="img-fluid rounded">
            </div>
            <div class="col-md-6 col-12">
              <div class="mb-2 text-muted small" id="informationModalFromBookmarksMeta"></div>
              <div class="mb-2"><strong>Facilitator/s:</strong> <span id="informationModalFromBookmarksFacilitators"></span></div>
              <div id="informationModalFromBookmarksDescription"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  @include('footer')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.bookmark-toggle').forEach(function(form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          var btn = form.querySelector('button');
          var original = btn.innerHTML;
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Removing';

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
              var row = form.closest('tr');
              if (row) {
                row.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateY(-6px)';
                setTimeout(function() { row.remove(); }, 350);
              }
              var alert = document.createElement('div');
              alert.className = 'alert alert-success position-fixed end-0 m-4 shadow-sm';
              alert.style.zIndex = 1050;
              alert.textContent = data.message || 'Updated';
              document.body.appendChild(alert);
              setTimeout(function() { alert.remove(); }, 2200);
            } else {
              alert((data && data.message) || 'Unexpected response');
            }
          }).catch(function(err) {
            console.error(err);
            alert('Failed to remove bookmark.');
          }).finally(function() {
            btn.disabled = false;
            btn.innerHTML = original;
          });
        });
      });
  
      // Open post modal when clicking open on a bookmarked post (if no external link)
      document.querySelectorAll('.open-post-from-bookmark').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var postId = btn.getAttribute('data-post-id');
          if (!postId) return;
          fetch("/posts/" + postId + "/json", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          }).then(function(res) { return res.json(); }).then(function(data) {
            // Try to find modal elements on the page (dashboard includes the modal)
            var modalEl = document.getElementById('postModal');
              if (!modalEl) {
                // Modal should exist on this page; if not, just abort
                console.warn('postModal not found');
                return;
              }
            // Populate modal
            var titleEl = modalEl.querySelector('#postModalLabel');
            var typeEl = modalEl.querySelector('#postModalType');
            var imageWrap = modalEl.querySelector('#postModalImageWrap');
            var descEl = modalEl.querySelector('#postModalDesc');
            var linksEl = modalEl.querySelector('#postModalLinks');

            titleEl.textContent = data.title || '';
            typeEl.textContent = data.type || '';
            imageWrap.innerHTML = '';
            var imageHtml = '';
            if (data.photo) {
              imageHtml = '<img src="' + data.photo + '" alt="" style="width:100%; height:auto;">';
            } else if (data.youtube_link) {
              var ytIdMatch = data.youtube_link.match(/v=([^&]+)/);
              var ytid = ytIdMatch ? ytIdMatch[1] : null;
              if (ytid) imageHtml = '<iframe src="https://www.youtube.com/embed/' + ytid + '" style="width:100%; min-height:240px;" allowfullscreen></iframe>';
            } else if (data.website_link) {
              // try og_image first, otherwise derive favicon from the site
              if (data.og_image) {
                imageHtml = '<img src="' + data.og_image + '" alt="" style="width:100%; height:auto;">';
              } else {
                try {
                  var url = new URL(data.website_link);
                  var favicon = url.protocol + '//' + url.hostname + '/favicon.ico';
                  imageHtml = '<img src="' + favicon + '" alt="" style="width:100%; height:auto;">';
                } catch (e) {
                  imageHtml = '';
                }
              }
            }
            imageWrap.innerHTML = imageHtml;
            descEl.textContent = data.description || '';
            linksEl.innerHTML = '';
            if (data.website_link) {
              var a = document.createElement('a');
              a.href = data.website_link;
              a.target = '_blank';
              a.className = 'btn btn-primary';
              a.textContent = 'Open website';
              linksEl.appendChild(a);
            }
            if (data.og_image) {
              var a2 = document.createElement('a');
              a2.href = data.og_image;
              a2.target = '_blank';
              a2.className = 'btn btn-outline-secondary';
              a2.textContent = 'Open image';
              linksEl.appendChild(a2);
            }

            var bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
          }).catch(function(err) {
            console.error(err);
            alert('Failed to open post.');
          });
        });
      });

      // Open information literacy modal when clicking open on an IL bookmarked item
      document.querySelectorAll('.open-info-from-bookmark').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var tr = btn.closest('tr');
          if (!tr) return;
          var dataWrap = tr.querySelector('.il-bookmark-data');
          if (!dataWrap) return;

          var title = dataWrap.querySelector('.il-title') ? dataWrap.querySelector('.il-title').textContent : '';
          var datetime = dataWrap.querySelector('.il-datetime') ? dataWrap.querySelector('.il-datetime').textContent : '';
          var type = dataWrap.querySelector('.il-type') ? dataWrap.querySelector('.il-type').textContent : '';
          var facilitators = dataWrap.querySelector('.il-facilitators') ? dataWrap.querySelector('.il-facilitators').textContent : '';
          var image = dataWrap.querySelector('.il-image') ? dataWrap.querySelector('.il-image').textContent : '';
          var desc = dataWrap.querySelector('.il-description') ? dataWrap.querySelector('.il-description').innerHTML : '';

          var modalEl = document.getElementById('informationModalFromBookmarks');
          if (!modalEl) return;
          modalEl.querySelector('#informationModalFromBookmarksLabel').textContent = title;
          modalEl.querySelector('#informationModalFromBookmarksMeta').textContent = datetime + ' | ' + type;
          modalEl.querySelector('#informationModalFromBookmarksFacilitators').textContent = facilitators;
          modalEl.querySelector('#informationModalFromBookmarksDescription').innerHTML = desc;

          var imageWrap = modalEl.querySelector('#informationModalFromBookmarksImageWrap');
          var imageEl = modalEl.querySelector('#informationModalFromBookmarksImage');
          if (image) {
            imageEl.src = image;
            imageWrap.style.display = '';
          } else {
            imageWrap.style.display = 'none';
            imageEl.src = '';
          }

          var bsModal = new bootstrap.Modal(modalEl);
          bsModal.show();
        });
      });
    });
  </script>
</body>
</html>
