<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $catalog->title ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Author</label>
        <input type="text" name="author" class="form-control" value="{{ old('author', $catalog->author ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Call Number</label>
        <input type="text" name="call_number" class="form-control" value="{{ old('call_number', $catalog->call_number ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Sub Location</label>
        <input type="text" name="sublocation" class="form-control" value="{{ old('sublocation', $catalog->sublocation ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Publisher</label>
        <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $catalog->publisher ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Year</label>
        <input type="text" name="year" class="form-control" value="{{ old('year', $catalog->year ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Edition</label>
        <input type="text" name="edition" class="form-control" value="{{ old('edition', $catalog->edition ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Format</label>
        <input type="text" name="format" class="form-control" value="{{ old('format', $catalog->format ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Unique Key</label>
        <input type="text" name="unique_key" class="form-control" value="{{ old('unique_key', $catalog->unique_key ?? '') }}" placeholder="Auto-generated if empty">
    </div>

    <div class="col-md-4">
        <label class="form-label">Content Type</label>
        <input type="text" name="content_type" class="form-control" value="{{ old('content_type', $catalog->content_type ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Media Type</label>
        <input type="text" name="media_type" class="form-control" value="{{ old('media_type', $catalog->media_type ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Carrier Type</label>
        <input type="text" name="carrier_type" class="form-control" value="{{ old('carrier_type', $catalog->carrier_type ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Copies Count</label>
        <input type="number" min="0" step="1" name="copies_count" class="form-control" value="{{ old('copies_count', $catalog->copies_count ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">ISBN</label>
        <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $catalog->isbn ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">ISSN</label>
        <input type="text" name="issn" class="form-control" value="{{ old('issn', $catalog->issn ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">LCCN</label>
        <input type="text" name="lccn" class="form-control" value="{{ old('lccn', $catalog->lccn ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Subjects</label>
        <textarea name="subjects" class="form-control" rows="2">{{ old('subjects', $catalog->subjects ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Additional Details</label>
        <textarea name="additional_details" class="form-control" rows="4">{{ old('additional_details', $catalog->additional_details ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">Cover Photo</label>
        <input type="file" name="cover_image" class="form-control" accept="image/*">
        <div class="form-text">Accepted: JPG, PNG, WEBP. Max size: 4MB.</div>
    </div>

    @if(!empty($catalog) && $catalog->cover_image)
    <div class="col-md-6">
        <label class="form-label d-block">Current Cover</label>
        <img src="{{ asset('storage/' . $catalog->cover_image) }}" alt="Current cover" style="width:85px;height:120px;object-fit:cover;border-radius:6px;">
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_cover_image" id="removeCoverImage" value="1">
            <label class="form-check-label" for="removeCoverImage">Remove current cover image</label>
        </div>
    </div>
    @endif
</div>
