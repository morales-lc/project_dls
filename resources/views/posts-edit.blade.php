@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<style>
    .quill-editor-wrapper .ql-toolbar {
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .quill-editor-wrapper .ql-container {
        min-height: 220px;
        border-radius: 0 0 0.5rem 0.5rem;
        font-size: 1rem;
    }

    .quill-editor-wrapper .ql-editor {
        min-height: 180px;
        max-height: 420px;
        overflow-y: auto;
    }

    .quill-editor-wrapper.quill-invalid .ql-toolbar,
    .quill-editor-wrapper.quill-invalid .ql-container {
        border-color: #dc3545;
    }
</style>
@endpush

@section('title', 'Edit Post / Announcement')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background:#fff;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ request('return', route('post.management')) }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Post Management</a>
            <span></span>
        </div>
        <h2 class="fw-bold mb-4 text-pink">Edit Post / Announcement</h2>
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow rounded-4 border-0">
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
                    <input type="hidden" name="return_url" value="{{ request('return', url()->previous()) }}">

                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="Announcement" {{ old('type', $post->type) == 'Announcement' ? 'selected' : '' }}>Announcement</option>
                                <option value="Event" {{ old('type', $post->type) == 'Event' ? 'selected' : '' }}>Event</option>
                                <option value="Update" {{ old('type', $post->type) == 'Update' ? 'selected' : '' }}>Update</option>
                                <option value="Post" {{ old('type', $post->type) == 'Post' ? 'selected' : '' }}>Post</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" maxlength="255" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <div class="quill-editor-wrapper @error('description') quill-invalid @enderror">
                                <div id="descriptionEditor" class="quill-editor"></div>
                            </div>
                            <input type="hidden" id="descriptionInput" name="description" value="{{ old('description', $post->description) }}">
                            <small class="text-muted">You can format text with headings, lists, and links.</small>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Media Type</label>
                            <select id="mediaType" class="form-select @error('media_type') is-invalid @enderror" name="media_type" required onchange="toggleMediaInputs()">
                                <option value="">Select Media Type</option>
                                <option value="image" {{ old('media_type', $post->photo ? 'image' : '') == 'image' ? 'selected' : '' }}>Image</option>
                                <option value="youtube" {{ old('media_type', $post->youtube_link ? 'youtube' : '') == 'youtube' ? 'selected' : '' }}>YouTube Link</option>
                                <option value="website" {{ old('media_type', $post->website_link ? 'website' : '') == 'website' ? 'selected' : '' }}>Website Link</option>
                            </select>
                            @error('media_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <div id="imageInput" style="display:none;">
                                <label class="form-label fw-semibold">Photo (Max: 5MB)</label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                                <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Leave empty to keep current image.</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($post->photo)
                                    <img src="{{ asset('storage/' . $post->photo) }}" alt="Photo" class="img-fluid rounded mt-2" style="max-height:120px; object-fit:cover;">
                                @endif
                            </div>

                            <div id="youtubeInput" style="display:none;">
                                <label class="form-label fw-semibold">YouTube Link</label>
                                <input type="url" name="youtube_link" class="form-control @error('youtube_link') is-invalid @enderror" value="{{ old('youtube_link', $post->youtube_link) }}" maxlength="500" placeholder="https://youtube.com/watch?v=...">
                                @error('youtube_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="websiteInput" style="display:none;">
                                <label class="form-label fw-semibold">Website Link</label>
                                <input type="url" name="website_link" class="form-control @error('website_link') is-invalid @enderror" value="{{ old('website_link', $post->website_link) }}" maxlength="500" placeholder="https://example.com">
                                @error('website_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col d-flex justify-content-end gap-2">
                            <a href="{{ request('return', route('post.management')) }}" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('management-scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    function initializeDescriptionEditor() {
        var editorEl = document.getElementById('descriptionEditor');
        var descriptionInput = document.getElementById('descriptionInput');
        if (!editorEl || !descriptionInput || typeof Quill === 'undefined') {
            return;
        }

        var quill = new Quill(editorEl, {
            theme: 'snow',
            placeholder: 'Write post description...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link'],
                    ['clean']
                ]
            }
        });

        var initialHtml = descriptionInput.value || '';
        if (initialHtml.trim() !== '') {
            quill.clipboard.dangerouslyPasteHTML(initialHtml);
        }

        function syncDescriptionField() {
            var html = quill.root.innerHTML.trim();
            descriptionInput.value = html === '<p><br></p>' ? '' : html;
        }

        quill.on('text-change', syncDescriptionField);
        syncDescriptionField();

        var form = descriptionInput.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                syncDescriptionField();
            });
        }
    }

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
    document.addEventListener('DOMContentLoaded', function() {
        toggleMediaInputs();
        initializeDescriptionEditor();
    });
</script>
@endpush
@endsection
