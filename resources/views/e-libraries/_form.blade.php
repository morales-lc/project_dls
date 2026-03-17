@csrf

<div class="mb-3">
    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $library->name ?? '') }}" required maxlength="255">
    <div class="form-text">Display name of the e-library.</div>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Description</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" maxlength="1000">{{ old('description', $library->description ?? '') }}</textarea>
    <div class="form-text">Maximum 1000 characters.</div>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Link (URL) <span class="text-danger">*</span></label>
    <input type="url" name="link" class="form-control @error('link') is-invalid @enderror"
        value="{{ old('link', $library->link ?? '') }}" required maxlength="2048" placeholder="https://example.com">
    @error('link') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Instructions</label>
    <input id="instructions" type="hidden" name="instructions"
        value="{{ old('instructions', $library->instructions ?? '') }}">
    <trix-editor input="instructions" class="bg-white border rounded shadow-sm p-2 @error('instructions') border-danger @enderror"></trix-editor>
    <div class="form-text">Provide usage steps or access guidelines.</div>
    @error('instructions') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Image (optional)</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
    <div class="form-text">Maximum size: 2MB. Supported formats: JPG, PNG, GIF.</div>
    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

    @isset($library)
    @if(!empty($library->image))
    <div class="mt-3">
        <span class="text-muted small d-block mb-2">Current Image:</span>
        <img src="{{ asset('storage/'.$library->image) }}"
            alt="Image"
            class="img-fluid rounded shadow-sm"
            style="max-height: 120px;">
    </div>
    @endif
    @endisset
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Username (optional)</label>
        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
            value="{{ old('username', $library->username ?? '') }}" maxlength="255">
        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Password (optional)</label>
        <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
            value="{{ old('password', $library->password ?? '') }}" maxlength="255">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <div class="form-text">These credentials will be displayed to users for easy copying.</div>
    </div>
</div>

@push('management-scripts')
<!-- Include Trix if not already -->
<link rel="stylesheet" href="https://unpkg.com/trix/dist/trix.css">
<script src="https://unpkg.com/trix/dist/trix.js"></script>
@endpush