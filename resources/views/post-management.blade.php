<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('navbar')
<div class="container py-5">
    <div class="mb-3">
        <button class="btn btn-outline-secondary" onclick="window.history.back()">
            &larr; Go Back
        </button>
    </div>
    <h2 class="fw-bold mb-4">Manage Posts & Announcements</h2>
    <div class="card mb-5 p-4 shadow-sm">
        <h4 class="fw-bold mb-3">Add New Post / Announcement</h4>
        <form method="POST" action="{{ route('dashboard.post.store') }}" enctype="multipart/form-data" class="card p-3 mb-4 shadow-sm">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="Announcement">Announcement</option>
                        <option value="Event">Event</option>
                        <option value="Update">Update</option>
                        <option value="Post">Post</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Media Type</label>
                    <select id="mediaType" class="form-select" name="media_type" required onchange="toggleMediaInputs()">
                        <option value="">Select Media Type</option>
                        <option value="image">Image</option>
                        <option value="youtube">YouTube Link</option>
                        <option value="website">Website Link</option>
                    </select>
                </div>
                <div class="col-md-4" id="imageInput" style="display:none;">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4" id="youtubeInput" style="display:none;">
                    <label class="form-label">YouTube Link</label>
                    <input type="url" name="youtube_link" class="form-control" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="col-md-4" id="websiteInput" style="display:none;">
                    <label class="form-label">Website Link</label>
                    <input type="url" name="website_link" class="form-control" placeholder="https://example.com">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Post</button>
                </div>
            </div>
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
        </form>
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
                            <div class="list-group-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-secondary me-2">{{ $post->type }}</span>
                                        <strong>{{ $post->title }}</strong>
                                    </div>
                                    <div>
                                        <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                                        <form method="POST" action="{{ route('post.delete', $post->id) }}" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    @if($post->photo)
                                        <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="img-fluid rounded mb-2" style="max-height:180px;object-fit:cover;">
                                    @endif
                                    @if($post->youtube_link)
                                        @php
                                            preg_match('/v=([^&]+)/', $post->youtube_link, $matches);
                                            $ytid = $matches[1] ?? null;
                                        @endphp
                                        @if($ytid)
                                            <div class="ratio ratio-16x9 mb-2" style="max-width:280px;">
                                                <iframe src="https://www.youtube.com/embed/{{ $ytid }}" title="YouTube video" allowfullscreen style="border-radius:8px;"></iframe>
                                            </div>
                                        @else
                                            <a href="{{ $post->youtube_link }}" target="_blank" class="d-block mb-2 text-center">
                                                <img src="https://img.youtube.com/vi/{{ $ytid }}/hqdefault.jpg" alt="YouTube Thumbnail" class="rounded" style="max-height:100px;width:auto;object-fit:cover;">
                                            </a>
                                        @endif
                                    @endif
                                    @if($post->website_link)
                                        @php
                                            $ogThumb = $post->og_image ?? null;
                                            $favicon = parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST) . '/favicon.ico';
                                        @endphp
                                        <a href="{{ $post->website_link }}" target="_blank" class="d-block mb-2 text-center">
                                            <img src="{{ $ogThumb ?: $favicon }}" alt="Website Thumbnail" class="img-fluid rounded mb-2" style="max-height:120px;object-fit:cover;display:inline-block;">
                                        </a>
                                    @endif
                                    <p class="text-muted mt-2">{{ $post->description }}</p>
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
