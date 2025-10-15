@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title','Add New Post')

@section('content')
    <div class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4 text-pink">Add New Post / Announcement</h2>
            <div class="card mb-5 shadow rounded-4 border-0" style="max-width:1200px;margin:auto;">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h4 class="fw-bold mb-0">Create Post / Announcement</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.post.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="Announcement">Announcement</option>
                                    <option value="Event">Event</option>
                                    <option value="Update">Update</option>
                                    <option value="Post">Post</option>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="row g-4 mt-1">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="row g-4 mt-1">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Media Type</label>
                                <select id="mediaType" class="form-select" name="media_type" required onchange="toggleMediaInputs()">
                                    <option value="">Select Media Type</option>
                                    <option value="image">Image</option>
                                    <option value="youtube">YouTube Link</option>
                                    <option value="website">Website Link</option>
                                </select>
                            </div>
                            <div class="col-md-9">
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
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
@endsection
