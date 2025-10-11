@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Edit Post / Announcement')

@section('content')
<div class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4 text-pink">Edit Post / Announcement</h2>

        <div class="card shadow rounded-4 border-0" style="max-width:1200px; margin:auto;">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h4 class="fw-bold mb-0">Update Post Details</h4>
            </div>

            <div class="card-body">
                {{-- Preview Section --}}
                <div class="mb-4">
                    @if($post->photo)
                        <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="img-fluid rounded mb-3" style="max-height:200px; object-fit:cover;">
                    @endif

                    @if($post->youtube_link)
                        @php
                            preg_match('/v=([^&]+)/', $post->youtube_link, $matches);
                            $ytid = $matches[1] ?? null;
                        @endphp
                        @if($ytid)
                            <div class="ratio ratio-16x9 mb-3">
                                <iframe src="https://www.youtube.com/embed/{{ $ytid }}" title="YouTube video" allowfullscreen></iframe>
                            </div>
                        @else
                            <a href="{{ $post->youtube_link }}" target="_blank" class="d-block mb-3">
                                <img src="https://img.youtube.com/vi/{{ $ytid }}/hqdefault.jpg" alt="YouTube Thumbnail" class="img-fluid rounded w-100" style="max-height:200px; object-fit:cover;">
                            </a>
                        @endif
                    @endif

                    @if($post->website_link)
                        @php
                            $ogThumb = $post->og_image ?? null;
                            $favicon = parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST) . '/favicon.ico';
                        @endphp
                        <a href="{{ $post->website_link }}" target="_blank" class="d-block mb-3">
                            <img src="{{ $ogThumb ?: $favicon }}" alt="Website Thumbnail" class="img-fluid rounded w-100" style="max-height:200px; object-fit:cover;">
                        </a>
                    @endif
                </div>

                {{-- Edit Form --}}
                <form method="POST" action="/post-management/{{ $post->id }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="Announcement" {{ $post->type == 'Announcement' ? 'selected' : '' }}>Announcement</option>
                                <option value="Event" {{ $post->type == 'Event' ? 'selected' : '' }}>Event</option>
                                <option value="Update" {{ $post->type == 'Update' ? 'selected' : '' }}>Update</option>
                                <option value="Post" {{ $post->type == 'Post' ? 'selected' : '' }}>Post</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" required>{{ $post->description }}</textarea>
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Media Type</label>
                            <select id="mediaType" class="form-select" name="media_type" required onchange="toggleMediaInputs()">
                                <option value="">Select Media Type</option>
                                <option value="image" {{ $post->photo ? 'selected' : '' }}>Image</option>
                                <option value="youtube" {{ $post->youtube_link ? 'selected' : '' }}>YouTube Link</option>
                                <option value="website" {{ $post->website_link ? 'selected' : '' }}>Website Link</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div id="imageInput" style="display:none;">
                                <label class="form-label fw-semibold">Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                @if($post->photo)
                                    <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="img-fluid rounded mt-2" style="max-height:120px; object-fit:cover;">
                                @endif
                            </div>

                            <div id="youtubeInput" style="display:none;">
                                <label class="form-label fw-semibold">YouTube Link</label>
                                <input type="url" name="youtube_link" class="form-control" value="{{ $post->youtube_link }}" placeholder="https://youtube.com/watch?v=...">
                            </div>

                            <div id="websiteInput" style="display:none;">
                                <label class="form-label fw-semibold">Website Link</label>
                                <input type="url" name="website_link" class="form-control" value="{{ $post->website_link }}" placeholder="https://example.com">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col d-flex justify-content-end gap-2">
                            <a href="{{ route('post.management') }}" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('management-scripts')
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
@endpush
@endsection
