<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $journal->title }} | SIDLAK Journal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidlak.css') }}" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        /* Scoped SIDLAK button theme fixes */
        .sidlak-page .btn-pink {
            background-color: #e83e8c; color:#fff; border:1px solid #e83e8c;
        }
        .sidlak-page .btn-pink:hover { background-color:#d63384; border-color:#d63384; color:#fff; }
        .sidlak-page .btn-outline-pink {
            background: transparent; color:#e83e8c; border:1.5px solid #e83e8c;
        }
        .sidlak-page .btn-outline-pink:hover { background:#fde7f0; color:#d63384; border-color:#d63384; }
    </style>
</head>

<body class="sidlak-page" style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #fce4ec 100%);">
    @include('navbar')
    <div class="container pt-4">
        <div class="mb-4">
            <a href="{{ route('sidlak.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Journals</a>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center mb-4">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 bg-white position-relative overflow-hidden sidlak-card-hover mb-4">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-5 text-center p-4">
                            @if($journal->cover_photo)
                            <img src="{{ asset('storage/' . $journal->cover_photo) }}" alt="Cover" class="img-fluid rounded-4 shadow" style="max-height:340px;object-fit:cover;border-bottom:4px solid #e83e8c;">
                            @else
                            <div class="bg-light border rounded-4 d-flex align-items-center justify-content-center mx-auto" style="height:340px;width:240px;">
                                <span class="text-muted">No Image</span>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-7 p-4">
                            <div class="d-flex align-items-start gap-3">
                                <h2 class="fw-bold mb-1" style="color:#e83e8c;">{{ $journal->title }}</h2>
                            </div>
                            <div class="mb-2">
                                <span class="badge px-3 py-2 fs-6 shadow" style="background:#e83e8c;">{{ $journal->month }} {{ $journal->year }}</span>
                            </div>
                            @if($journal->print_issn)
                            <div class="mb-2 text-muted small">ISSN: <span class="fw-semibold">{{ $journal->print_issn }}</span></div>
                            @endif
                            @if(Auth::check() && Auth::user()->role !== 'guest')
                                @php
                                    // Use controller-provided variable if available
                                    $journalBookmarked = $journalBookmarked ?? false;
                                @endphp
                                <form action="{{ route('bookmarks.toggle') }}" method="POST" class="sidlak-journal-bookmark-toggle mt-2">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $journal->id }}">
                                    <input type="hidden" name="type" value="sidlak_journal">
                                    <button type="submit" class="btn btn-sm {{ $journalBookmarked ? 'btn-pink' : 'btn-outline-pink' }}">
                                        <i class="bi {{ $journalBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                                        <span>{{ $journalBookmarked ? 'Bookmarked' : 'Bookmark Journal' }}</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="card border-0 shadow-lg rounded-4 mb-4">
            <div class="card-body p-4">
                <ul class="nav nav-tabs mb-4 border-0" id="sidlakTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab" aria-controls="articles" aria-selected="true" style="color:#e83e8c;font-weight:600;">Articles</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab" aria-controls="about" aria-selected="false" style="color:#e83e8c;font-weight:600;">About</button>
                </ul>
                <div class="tab-content" id="sidlakTabContent">
                    <div class="tab-pane fade" id="about" role="tabpanel" aria-labelledby="about-tab">
                        <div class="mb-4">
                            <h5 class="fw-bold">About the Journal</h5>
                            <div class="mb-2 fs-4 fw-bold">SIDLAK</div>
                            <div class="mb-2">The Lourdes College Multidisciplinary Research Journal</div>
                            <div class="mb-2">{{ $journal->title }} {{ $journal->month }} {{ $journal->year }}</div>
                            <div class="mb-2">Print ISSN {{ $journal->print_issn }}</div>
                            <div class="mb-2">Published by the Research, Planning, and Development Office, Lourdes College, Cagayan de Oro City</div>
                        </div>
                        <h4 class="fw-bold mb-3" style="color:#e83e8c;">Editors</h4>
                        <div class="mb-4">
                            @if($journal->editors->count())
                            <div>
                                @foreach($journal->editors as $editor)
                                <div class="mb-1">
                                    <span class="fw-normal">{{ $editor->name }}</span>
                                    <span class="badge bg-primary ms-2">{{ $editor->title }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-muted">No editors listed.</div>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-3" style="color:#e83e8c;">Peer Reviewers</h4>
                        <div>
                            @if($journal->peerReviewers->count())
                            <div>
                                @foreach($journal->peerReviewers as $reviewer)
                                <div class="mb-3">
                                    <span class="fw-normal">{{ $reviewer->name }}</span>
                                    <span class="badge bg-secondary ms-2">{{ $reviewer->title }}</span><br>
                                    <span class="fst-italic">{{ $reviewer->institution }}</span><br>
                                    <span class="fst-italic">{{ $reviewer->city }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-muted">No peer reviewers listed.</div>
                            @endif
                        </div>
                        <div class="mt-5 text-center text-muted small">
                            All rights reserved. No part of this journal may be reproduced in any form or by any means without the written permission from the publisher
                        </div>
                        <div class="mt-3 text-center small">
                            <strong>SIDLAK</strong> (effulgence or radiance) refers to the light that radiates from the sun. It also refers to the quality of being bright and sending out rays of light. SIDLAK publishes relevant studies in various disciplines to advance the development of knowledge and inform diverse professional practice. This volume presents studies in social work, library and information science, hospitality management, home economics, and language teaching.
                        </div>
                    </div>
                    <div class="tab-pane fade show active" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                        <h4 class="fw-bold mb-3" style="color:#e83e8c;">Research Articles</h4>
                        <div class="row">
                            @foreach($journal->articles as $article)
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow sidlak-card-hover h-100">
                                    <div class="card-body">
                                        <h5 class="fw-bold" style="color:#e83e8c;">{{ $article->title }}</h5>
                                        <div class="mb-2 text-muted">Authors: {{ $article->authors }}</div>
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <a href="{{ route('sidlak.article.download', $article->id) }}" target="_blank" class="btn btn-outline-success">Download PDF</a>
                                            @if(Auth::check() && Auth::user()->role !== 'guest')
                                                        @php
                                                            // Use controller-provided list of bookmarked article IDs if available
                                                            $isBookmarked = isset($bookmarkedArticleIds) && is_array($bookmarkedArticleIds) ? in_array($article->id, $bookmarkedArticleIds) : false;
                                                        @endphp
                                                        <form action="{{ route('bookmarks.toggle') }}" method="POST" class="d-inline sidlak-bookmark-toggle" data-article-id="{{ $article->id }}">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $article->id }}">
                                                            <input type="hidden" name="type" value="sidlak">
                                                            <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-pink' : 'btn-outline-pink' }} sidlak-bookmark-btn">
                                                                <i class="bi {{ $isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                                                                <span class="btn-label">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                                                            </button>
                                                        </form>
                                            @elseif(!Auth::check())
                                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Login to bookmark</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-5 text-center text-muted small">
                            All rights reserved. No part of this journal may be reproduced in any form or by any means without the written permission from the publisher
                        </div>
                        <div class="mt-3 text-center small">
                            <strong>SIDLAK</strong> (effulgence or radiance) refers to the light that radiates from the sun. It also refers to the quality of being bright and sending out rays of light. SIDLAK publishes relevant studies in various disciplines to advance the development of knowledge and inform diverse professional practice. This volume presents studies in social work, library and information science, hospitality management, home economics, and language teaching.
                        </div>
                    </div>
                </div>

            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </div>

    </div>


    <div class="mt-5"></div>
    @include('footer')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Journal-level bookmark toggle
            document.querySelectorAll('.sidlak-journal-bookmark-toggle').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = form.querySelector('button');
                    var icon = btn.querySelector('i');
                    var label = btn.querySelector('span');
                    var original = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

                    var fd = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: fd
                    }).then(function(res) { return res.json(); }).then(function(data) {
                        if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                            var bookmarked = data.status === 'bookmarked';
                            if (bookmarked) {
                                btn.classList.remove('btn-outline-pink');
                                btn.classList.add('btn-pink');
                                icon.classList.remove('bi-bookmark');
                                icon.classList.add('bi-bookmark-fill');
                                label.textContent = 'Bookmarked';
                            } else {
                                btn.classList.remove('btn-pink');
                                btn.classList.add('btn-outline-pink');
                                icon.classList.remove('bi-bookmark-fill');
                                icon.classList.add('bi-bookmark');
                                label.textContent = 'Bookmark Journal';
                            }
                        }
                    }).catch(function(err) { console.error(err); alert('Failed to toggle bookmark.'); })
                      .finally(function() { btn.disabled = false; btn.innerHTML = original; });
                });
            });
            document.querySelectorAll('.sidlak-bookmark-toggle').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = form.querySelector('.sidlak-bookmark-btn');
                    var icon = btn.querySelector('i');
                    var label = btn.querySelector('.btn-label');
                    var originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

                    var formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    }).then(function(res) { return res.json(); }).then(function(data) {
                        if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                            var isNowBookmarked = data.status === 'bookmarked';
                            if (isNowBookmarked) {
                                btn.classList.remove('btn-outline-pink');
                                btn.classList.add('btn-pink');
                                icon.classList.remove('bi-bookmark');
                                icon.classList.add('bi-bookmark-fill');
                                label.textContent = 'Bookmarked';
                            } else {
                                btn.classList.remove('btn-pink');
                                btn.classList.add('btn-outline-pink');
                                icon.classList.remove('bi-bookmark-fill');
                                icon.classList.add('bi-bookmark');
                                label.textContent = 'Bookmark';
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
                        alert('Failed to toggle bookmark.');
                    }).finally(function() {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
                });
            });
        });
    </script>
</body>

</html>