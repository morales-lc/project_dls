@csrf

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Title</label>
        <input
            type="text"
            name="title"
            value="{{ old('title', $yearbook->title ?? '') }}"
            class="form-control @error('title') is-invalid @enderror"
            maxlength="255"
            required
        >
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Year</label>
        <input
            type="number"
            name="year"
            value="{{ old('year', $yearbook->year ?? '') }}"
            class="form-control @error('year') is-invalid @enderror"
            min="1900"
            max="{{ now()->year + 1 }}"
            required
        >
        @error('year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Yearbook PDF {{ isset($yearbook) ? '(optional when editing)' : '' }}</label>
        <input
            type="file"
            name="pdf_file"
            accept="application/pdf"
            class="form-control @error('pdf_file') is-invalid @enderror"
            {{ isset($yearbook) ? '' : 'required' }}
        >
        @error('pdf_file')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">PDF only, up to 20MB.</small>

        @if(isset($yearbook) && $yearbook->pdf_file)
            <div class="mt-2">
                <a href="{{ asset('storage/' . $yearbook->pdf_file) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf"></i> View current PDF
                </a>
            </div>
        @endif
    </div>
</div>
