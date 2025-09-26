<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body style="min-height: 100vh; background-color: #f8f9fa;">
    <div id="dashboardWrapper" class="d-flex position-relative">
        @include('components.admin-sidebar')
        <div class="flex-grow-1">
            @include('navbar')
            <div class="container py-5">
                <div class="mb-3">
                    <button class="btn btn-outline-secondary" onclick="window.history.back()">
                        &larr; Go Back
                    </button>
                </div>
                <h2 class="fw-bold mb-4">Manage Posts & Announcements</h2>
                <div class="card mb-5 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h4 class="fw-bold mb-0">Add New Post / Announcement</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('dashboard.post.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="Announcement">Announcement</option>
                                        <option value="Event">Event</option>
                                        <option value="Update">Update</option>
                                        <option value="Post">Post</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                            <div class="row g-4 mt-1">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="row g-4 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Media Type</label>
                                    <select id="mediaType" class="form-select" name="media_type" required onchange="toggleMediaInputs()">
                                        <option value="">Select Media Type</option>
                                        <option value="image">Image</option>
                                        <option value="youtube">YouTube Link</option>
                                        <option value="website">Website Link</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div id="imageInput" style="display:none;">
                                        <label class="form-label fw-semibold">Photo</label>
                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                    </div>
                                    <div id="youtubeInput" style="display:none;">
                                        <label class="form-label fw-semibold">YouTube Link</label>
                                        <input type="url" name="youtube_link" class="form-control" placeholder="https://youtube.com/watch?v=...">
                                    </div>
                                    <div id="websiteInput" style="display:none;">
                                        <label class="form-label fw-semibold">Website Link</label>
                                        <input type="url" name="website_link" class="form-control" placeholder="https://example.com">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-primary px-4">Post</button>
                                </div>
                            </div>
                        </form>
                        <script>
                            function toggleMediaInputs() {
                                var type = document.getElementById('mediaType').value;
                                var imageInput = document.getElementById('imageInput');
                                var youtubeInput = document.getElementById('youtubeInput');
                                var websiteInput = document.getElementById('websiteInput');
                                imageInput.style.display = (type === 'image') ? 'block' : 'none';
                                youtubeInput.style.display = (type === 'youtube') ? 'block' : 'none';
                                websiteInput.style.display = (type === 'website') ? 'block' : 'none';

                                // Clear other media fields
                                if (type !== 'image') {
                                    var photoField = imageInput.querySelector('input[name="photo"]');
                                    if (photoField) photoField.value = '';
                                }
                                if (type !== 'youtube') {
                                    var ytField = youtubeInput.querySelector('input[name="youtube_link"]');
                                    if (ytField) ytField.value = '';
                                }
                                if (type !== 'website') {
                                    var webField = websiteInput.querySelector('input[name="website_link"]');
                                    if (webField) webField.value = '';
                                }
                            }
                        </script>
                    </div>
                </div>
                @php
                $types = ['Announcement', 'Event', 'Update', 'Post'];
                @endphp
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="postTypeTabs" role="tablist">
                    @foreach($types as $i => $type)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($i === 0) active @endif" id="tab-{{ strtolower($type) }}" data-bs-toggle="tab" data-bs-target="#pane-{{ strtolower($type) }}" type="button" role="tab" aria-controls="pane-{{ strtolower($type) }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                            {{ $type == 'Post' ? 'Latest Posts' : $type . 's' }}
                        </button>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content" id="postTypeTabsContent">
                    @foreach($types as $i => $type)
                    <div class="tab-pane fade @if($i === 0) show active @endif" id="pane-{{ strtolower($type) }}" role="tabpanel" aria-labelledby="tab-{{ strtolower($type) }}">
                        <div class="card shadow-sm border-0 mb-5">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">{{ $type == 'Post' ? 'Latest Posts' : $type . 's' }}</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($posts) && $posts->where('type', $type)->count())
                                <div class="list-group">
                                    @foreach($posts->where('type', $type) as $post)
                                    @php
                                    $thumbUrl = null;
                                    $ytid = null;
                                    if ($post->photo) {
                                        $thumbUrl = asset('storage/' . $post->photo);
                                    }
                                    if (!$thumbUrl && $post->youtube_link) {
                                        preg_match('/v=([^&]+)/', $post->youtube_link, $m);
                                        $ytid = $m[1] ?? null;
                                        // Fallback to thumbnail only if we cannot extract a YouTube video id
                                        if (!$ytid) {
                                            $thumbUrl = 'https://img.youtube.com/vi/' . ($m[1] ?? 'unknown') . '/hqdefault.jpg';
                                        }
                                    }
                                    if (!$thumbUrl && !$ytid && $post->website_link) {
                                        $ogThumb = $post->og_image ?? null;
                                        $base = parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST);
                                        $thumbUrl = $ogThumb ?: ($base . '/favicon.ico');
                                    }
                                    @endphp
                                    <div class="list-group-item mb-3 p-3" style="min-height: 220px; display: flex; align-items: stretch;">
                                        <div class="row g-3 w-100 align-items-start">
                                            <div class="col-md-4 d-flex flex-column align-items-center">
                                                <span class="badge bg-secondary mb-2 align-self-start">{{ $post->type }}</span>
                                                <div class="mt-1 w-100">
                                                    @if($ytid)
                                                    <div class="rounded overflow-hidden w-100" style="max-width: 380px;">
                                                        <iframe src="https://www.youtube.com/embed/{{ $ytid }}" title="YouTube video" allowfullscreen style="width:100%; height:220px; border:0; border-radius:8px;"></iframe>
                                                    </div>
                                                    @elseif($thumbUrl)
                                                    <img src="{{ $thumbUrl }}" alt="Thumbnail" class="img-fluid rounded w-100" style="height:220px;object-fit:cover;max-width:380px;">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-8 d-flex flex-column justify-content-between" style="min-height: 220px;">
                                                <div class="d-flex flex-column justify-content-start h-100 mt-md-4">
                                                    <h5 class="mb-2">{{ $post->title }}</h5>
                                                    <p class="text-muted mb-0">{{ $post->description }}</p>
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end align-items-end mt-2">
                                                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                                    <form method="POST" action="{{ route('post.delete', $post->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center text-muted">No {{ $type == 'Post' ? 'posts' : strtolower($type) . 's' }} yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>