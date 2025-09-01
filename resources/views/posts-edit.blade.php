<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body style="min-height: 100vh; background-color: #f8f9fa;">
@include('navbar')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Edit Post / Announcement</h2>
    <div class="card p-4 shadow-sm">
        <div class="mb-4">
            @if($post->photo)
                <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="img-fluid rounded mb-2" style="max-height:180px;object-fit:cover;">
            @endif
            @if($post->youtube_link)
                @php
                    preg_match('/v=([^&]+)/', $post->youtube_link, $matches);
                    $ytid = $matches[1] ?? null;
                @endphp
                @if($ytid)
                    <div class="ratio ratio-16x9 mb-2">
                        <iframe src="https://www.youtube.com/embed/{{ $ytid }}" title="YouTube video" allowfullscreen></iframe>
                    </div>
                @else
                    <a href="{{ $post->youtube_link }}" target="_blank" class="d-block mb-2">
                        <img src="https://img.youtube.com/vi/{{ $ytid }}/hqdefault.jpg" alt="YouTube Thumbnail" class="img-fluid rounded w-100" style="max-height:180px;object-fit:cover;">
                    </a>
                @endif
            @endif
            @if($post->website_link)
                @php
                    $ogThumb = $post->og_image ?? null;
                    $favicon = parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST) . '/favicon.ico';
                @endphp
                <a href="{{ $post->website_link }}" target="_blank" class="d-block mb-2">
                    <img src="{{ $ogThumb ?: $favicon }}" alt="Website Thumbnail" class="img-fluid rounded w-100" style="max-height:180px;object-fit:cover;">
                </a>
            @endif
        </div>
    <form method="POST" action="/post-management/{{ $post->id }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="Announcement" {{ $post->type == 'Announcement' ? 'selected' : '' }}>Announcement</option>
                        <option value="Event" {{ $post->type == 'Event' ? 'selected' : '' }}>Event</option>
                        <option value="Update" {{ $post->type == 'Update' ? 'selected' : '' }}>Update</option>
                        <option value="Post" {{ $post->type == 'Post' ? 'selected' : '' }}>Post</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" required>{{ $post->description }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Media Type</label>
                    <select id="mediaType" class="form-select" name="media_type" required onchange="toggleMediaInputs()">
                        <option value="">Select Media Type</option>
                        <option value="image" {{ $post->photo ? 'selected' : '' }}>Image</option>
                        <option value="youtube" {{ $post->youtube_link ? 'selected' : '' }}>YouTube Link</option>
                        <option value="website" {{ $post->website_link ? 'selected' : '' }}>Website Link</option>
                    </select>
                </div>
                <div class="col-md-4" id="imageInput" style="display:none;">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if($post->photo)
                        <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="img-fluid rounded mt-2" style="max-height:120px;object-fit:cover;">
                    @endif
                </div>
                <div class="col-md-4" id="youtubeInput" style="display:none;">
                    <label class="form-label">YouTube Link</label>
                    <input type="url" name="youtube_link" class="form-control" value="{{ $post->youtube_link }}" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="col-md-4" id="websiteInput" style="display:none;">
                    <label class="form-label">Website Link</label>
                    <input type="url" name="website_link" class="form-control" value="{{ $post->website_link }}" placeholder="https://example.com">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                    <a href="{{ route('post.management') }}" class="btn btn-secondary w-100">Cancel</a>
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
                window.onload = function() {
                    toggleMediaInputs();
                };
            </script>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
