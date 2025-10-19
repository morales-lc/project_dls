@csrf

<div class="mb-3">
    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control"
        value="{{ old('name', $library->name ?? '') }}" required>
    <div class="form-text">Display name of the e-library.</div>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $library->description ?? '') }}</textarea>
    @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Link (URL) <span class="text-danger">*</span></label>
    <input type="url" name="link" class="form-control"
        value="{{ old('link', $library->link ?? '') }}" required>
    @error('link') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Instructions</label>
    <input id="instructions" type="hidden" name="instructions"
        value="{{ old('instructions', $library->instructions ?? '') }}">
    <trix-editor input="instructions" class="bg-white border rounded shadow-sm p-2"></trix-editor>
    <div class="form-text">Provide usage steps or access guidelines.</div>
    @error('instructions') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Image (optional)</label>
    <input type="file" name="image" class="form-control" accept="image/*">
    @error('image') <div class="text-danger small">{{ $message }}</div> @enderror

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
        <input type="text" name="username" class="form-control"
            value="{{ old('username', $library->username ?? '') }}">
        @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Password (optional)</label>
        <input type="text" name="password" class="form-control"
            value="{{ old('password', $library->password ?? '') }}">
        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
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