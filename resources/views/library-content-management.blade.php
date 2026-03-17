@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Library Content')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<style>
    .ann-item { cursor: grab; }
    .ann-item.dragging { opacity: .6; }
    .gif-preview { max-width: 360px; border: 2px solid #ffd1e3; border-radius: .5rem; }
    .text-pink { color: #d81b60; }
    .btn-outline-pink { border-color: #d81b60; color: #d81b60; }
    .btn-outline-pink:hover { background:#d81b60; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold text-pink mb-0">Library Content</h2>
            <small class="text-muted">Manage Library Hours GIF, announcements, and contact information</small>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-pink">Back to Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h5 class="text-pink fw-bold">Library Hours GIF</h5>
                    <p class="text-muted small">Only one GIF is used on the homepage. Upload a new one or remove the current GIF.</p>

                    <div class="mb-3">
                        @if($settings && $settings->library_hours_gif)
                            <img src="{{ asset('storage/' . $settings->library_hours_gif) }}" alt="Current Library Hours GIF" class="gif-preview shadow-sm" />
                        @else
                            <img src="{{ asset('images/servicehours.gif') }}" alt="Default Library Hours GIF" class="gif-preview shadow-sm" />
                            <div class="small text-muted mt-2">Showing default GIF bundled with the app.</div>
                        @endif
                    </div>

                    <form action="{{ route('library.content.gif') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                        @csrf
                        <div>
                            <input type="file" name="gif" accept="image/gif" class="form-control @error('gif') is-invalid @enderror" />
                            <small class="text-muted">Max 4 MB; GIF only.</small>
                            @error('gif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-pink">Save GIF</button>
                            @if($settings && $settings->library_hours_gif)
                                <button type="submit" name="remove_gif" value="1" class="btn btn-outline-danger" onclick="return confirm('Remove current GIF?')">Remove GIF</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="text-pink fw-bold mb-0">Library Announcements</h5>
                    </div>

                    <form action="{{ route('library.content.announcements.store') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="text" class="form-control @error('text') is-invalid @enderror" placeholder="Add an announcement..." required maxlength="500" value="{{ old('text') }}">
                            <button class="btn btn-outline-pink" type="submit">Add</button>
                        </div>
                        @error('text') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="annActive" name="active" checked>
                            <label class="form-check-label" for="annActive">Active</label>
                        </div>
                    </form>

                    <ul id="ann-list" class="list-group">
                        @forelse($announcements as $ann)
                            <li class="list-group-item d-flex align-items-center justify-content-between ann-item" data-id="{{ $ann->id }}">
                                <div class="flex-grow-1 me-3">
                                    <form action="{{ route('library.content.announcements.update', $ann->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <i class="bi bi-grip-vertical text-muted"></i>
                                        <input type="text" name="text" value="{{ $ann->text }}" class="form-control" maxlength="500">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="active" value="1" {{ $ann->active ? 'checked' : '' }}>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <button class="btn btn-sm btn-outline-pink" type="submit">Save</button>
                                    </form>
                                </div>
                                <form action="{{ route('library.content.announcements.delete', $ann->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No announcements yet.</li>
                        @endforelse
                    </ul>

                    <div class="mt-3 text-muted small">Drag-and-drop to reorder announcements.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slideshow Images Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h3 class="text-pink fw-bold mb-1">Homepage Slideshow Images</h3>
                            <small class="text-muted">Manage images displayed in the dashboard slideshow</small>
                        </div>
                    </div>

                    <form action="{{ route('library.content.slideshow.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror" required>
                                <small class="text-muted">Max 5 MB; JPG, PNG, or GIF</small>
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Caption (Optional)</label>
                                <input type="text" name="caption" class="form-control @error('caption') is-invalid @enderror" placeholder="Image caption..." maxlength="255" value="{{ old('caption') }}">
                                @error('caption') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="slideActive" name="active" checked>
                                    <label class="form-check-label" for="slideActive">Active</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-outline-pink" type="submit">
                                    <i class="bi bi-upload"></i> Upload Image
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="row g-3" id="slideshow-list">
                        @forelse($slideshowImages as $slide)
                            <div class="col-md-4 col-lg-3 slideshow-item" data-id="{{ $slide->id }}">
                                <div class="card h-100 shadow-sm">
                                    <img src="{{ asset('storage/' . $slide->image_path) }}" class="card-img-top" alt="{{ $slide->caption }}" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <form action="{{ route('library.content.slideshow.update', $slide->id) }}" method="POST" class="mb-2">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-2">
                                                <input type="text" name="caption" value="{{ $slide->caption }}" class="form-control form-control-sm" placeholder="Caption..." maxlength="255">
                                            </div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="form-check form-check-sm">
                                                    <input class="form-check-input" type="checkbox" name="active" value="1" {{ $slide->active ? 'checked' : '' }} id="slide-active-{{ $slide->id }}">
                                                    <label class="form-check-label small" for="slide-active-{{ $slide->id }}">Active</label>
                                                </div>
                                                <button class="btn btn-sm btn-outline-pink" type="submit">Save</button>
                                            </div>
                                        </form>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-secondary"><i class="bi bi-grip-vertical"></i> Drag</span>
                                            <form action="{{ route('library.content.slideshow.delete', $slide->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Delete this image?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger w-100" type="submit">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-0">No slideshow images yet. Upload your first image above.</div>
                            </div>
                        @endforelse
                    </div>

                    @if($slideshowImages->isNotEmpty())
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle"></i> Drag-and-drop images to reorder them in the slideshow.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Netzone Management Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h3 class="text-pink fw-bold mb-1">Netzone Settings</h3>
                    <small class="text-muted">Manage Netzone page content, images, and reminders</small>

                    <form action="{{ route('library.content.netzone.update') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $netzoneSettings->title) }}" required maxlength="255">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required maxlength="5000">{{ old('description', $netzoneSettings->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-pink">Update Netzone Info</button>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Netzone Images (Slideshow)</h6>
                    <form action="{{ route('library.content.netzone.image.add') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                            <button type="submit" class="btn btn-outline-pink">Add Image</button>
                        </div>
                        <small class="text-muted">Max 5 MB; JPG, PNG, or GIF</small>
                        @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </form>

                    <div class="row g-3">
                        @forelse($netzoneSettings->images ?? [] as $index => $imagePath)
                        <div class="col-md-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $imagePath) }}" class="card-img-top" alt="Netzone Image" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <form action="{{ route('library.content.netzone.image.delete') }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted">No images added yet.</p>
                        </div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Netzone Reminders</h6>
                    <form action="{{ route('library.content.netzone.reminder.add') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="text" name="text" class="form-control @error('text') is-invalid @enderror" placeholder="Reminder text..." required maxlength="1000" value="{{ old('text') }}">
                                @error('text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="danger">Danger</option>
                                    <option value="warning" selected>Warning</option>
                                    <option value="info">Info</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-pink w-100">Add</button>
                            </div>
                        </div>
                    </form>

                    <ul class="list-group">
                        @forelse($netzoneSettings->reminders ?? [] as $index => $reminder)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <span class="badge bg-{{ $reminder['type'] }} me-2">{{ $index + 1 }}</span>
                                    <span>{{ $reminder['text'] }}</span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editReminderModal{{ $index }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('library.content.netzone.reminder.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this reminder?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editReminderModal{{ $index }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('library.content.netzone.reminder.update') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="index" value="{{ $index }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Reminder</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Text</label>
                                                    <textarea name="text" class="form-control" rows="3" required>{{ $reminder['text'] }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="danger" {{ $reminder['type'] == 'danger' ? 'selected' : '' }}>Danger</option>
                                                        <option value="warning" {{ $reminder['type'] == 'warning' ? 'selected' : '' }}>Warning</option>
                                                        <option value="info" {{ $reminder['type'] == 'info' ? 'selected' : '' }}>Info</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-pink">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">No reminders added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Learning Spaces Management Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h3 class="text-pink fw-bold mb-1">Learning Spaces Settings</h3>
                    <small class="text-muted">Manage Learning Spaces page content and images</small>

                    <form action="{{ route('library.content.learning-space.update') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $learningSpaceSettings->title) }}" required maxlength="255">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required maxlength="5000">{{ old('description', $learningSpaceSettings->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-pink">Update Learning Space Info</button>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Learning Space Images (Slideshow)</h6>
                    <form action="{{ route('library.content.learning-space.image.add') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                            <button type="submit" class="btn btn-outline-pink">Add Image</button>
                        </div>
                        @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <small class="text-muted">Max 5 MB; JPG, PNG, or GIF</small>
                    </form>

                    <div class="row g-3">
                        @forelse($learningSpaceSettings->images ?? [] as $index => $imagePath)
                        <div class="col-md-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $imagePath) }}" class="card-img-top" alt="Learning Space Image" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <form action="{{ route('library.content.learning-space.image.delete') }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted">No images added yet.</p>
                        </div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Content Sections</h6>
                    
                    <!-- Add New Section Form -->
                    <form action="{{ route('library.content.learning-space.section.add') }}" method="POST" class="mb-4 p-3 bg-light rounded">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="text" name="heading" class="form-control @error('heading') is-invalid @enderror" placeholder="Section heading (e.g., Types of Learning Spaces)..." required maxlength="255" value="{{ old('heading') }}">
                                @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="list">Bullet List</option>
                                    <option value="numbered">Numbered List</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-pink w-100">Add Section</button>
                            </div>
                        </div>
                    </form>

                    <!-- Display Existing Sections -->
                    @forelse($learningSpaceSettings->content_sections ?? [] as $sectionIndex => $section)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <strong>{{ $section['heading'] }}</strong>
                                    <span class="badge bg-info ms-2">{{ $section['type'] === 'list' ? 'Bullet List' : 'Numbered List' }}</span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $sectionIndex }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('library.content.learning-space.section.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this entire section?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="index" value="{{ $sectionIndex }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Add Item Form -->
                            <form action="{{ route('library.content.learning-space.section.item.add') }}" method="POST" class="mb-3">
                                @csrf
                                <input type="hidden" name="section_index" value="{{ $sectionIndex }}">
                                <div class="input-group">
                                    <input type="text" name="item_text" class="form-control" placeholder="Add new item..." required>
                                    <button type="submit" class="btn btn-sm btn-outline-pink">Add Item</button>
                                </div>
                            </form>

                            <!-- Display Items -->
                            <ul class="list-group">
                                @forelse($section['items'] ?? [] as $itemIndex => $item)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <span class="badge bg-secondary me-2">{{ $itemIndex + 1 }}</span>
                                            <span>{!! $item !!}</span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $sectionIndex }}_{{ $itemIndex }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('library.content.learning-space.section.item.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="section_index" value="{{ $sectionIndex }}">
                                                <input type="hidden" name="item_index" value="{{ $itemIndex }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Item Modal -->
                                    <div class="modal fade" id="editItemModal{{ $sectionIndex }}_{{ $itemIndex }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('library.content.learning-space.section.item.update') }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="section_index" value="{{ $sectionIndex }}">
                                                    <input type="hidden" name="item_index" value="{{ $itemIndex }}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Item</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Item Text</label>
                                                            <textarea name="item_text" class="form-control" rows="3" required>{{ strip_tags($item) }}</textarea>
                                                            <small class="text-muted">You can use HTML tags like &lt;strong&gt; for bold text.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-pink">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-muted">No items in this section yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Edit Section Modal -->
                    <div class="modal fade" id="editSectionModal{{ $sectionIndex }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('library.content.learning-space.section.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="index" value="{{ $sectionIndex }}">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Section</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Section Heading</label>
                                            <input type="text" name="heading" class="form-control" value="{{ $section['heading'] }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">List Type</label>
                                            <select name="type" class="form-select" required>
                                                <option value="list" {{ $section['type'] === 'list' ? 'selected' : '' }}>Bullet List</option>
                                                <option value="numbered" {{ $section['type'] === 'numbered' ? 'selected' : '' }}>Numbered List</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-pink">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No content sections yet. Add your first section above (e.g., "Types of Learning Spaces", "How to Reserve a Space").
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Book Borrowing Management Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-pink fw-bold mb-1">Book Borrowing Settings</h3>
                    <small class="text-muted">Manage Book Borrowing page content and images</small>
                    
                    <form action="{{ route('library.content.book-borrowing.update') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Page Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $bookBorrowingSettings->title) }}" required maxlength="255">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-pink">Update Title</button>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Book Borrowing Images (Slideshow)</h6>
                    <form action="{{ route('library.content.book-borrowing.image.add') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                            <button type="submit" class="btn btn-pink">Add Image</button>
                        </div>
                        <small class="text-muted">Max 5 MB; JPG, PNG, or GIF</small>
                        @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </form>

                    <div class="row g-3">
                        @forelse($bookBorrowingSettings->images ?? [] as $index => $imagePath)
                        <div class="col-md-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $imagePath) }}" class="card-img-top" alt="Book Borrowing Image" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <form action="{{ route('library.content.book-borrowing.image.delete') }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">No images uploaded yet.</div>
                        </div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Borrowing Steps</h6>
                    <form action="{{ route('library.content.book-borrowing.step.add') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="type" value="borrowing">
                        <div class="input-group">
                            <textarea name="step" class="form-control @error('step') is-invalid @enderror" placeholder="Enter a new borrowing step (HTML allowed)" rows="2" required maxlength="1000">{{ old('step') }}</textarea>
                            <button type="submit" class="btn btn-pink">Add Step</button>
                        </div>
                        <small class="text-muted">HTML tags allowed for formatting</small>
                        @error('step') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </form>

                    <ul class="list-group">
                        @forelse($bookBorrowingSettings->borrowing_steps ?? [] as $index => $step)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong>{{ $index + 1 }}.</strong> {!! $step !!}
                            </div>
                            <div class="btn-group btn-group-sm ms-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editBorrowingStepModal{{ $index }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('library.content.book-borrowing.step.delete') }}" method="POST" onsubmit="return confirm('Delete this step?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    <input type="hidden" name="type" value="borrowing">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editBorrowingStepModal{{ $index }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('library.content.book-borrowing.step.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <input type="hidden" name="type" value="borrowing">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Borrowing Step {{ $index + 1 }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea name="step" class="form-control" rows="3" required>{{ $step }}</textarea>
                                            <small class="text-muted">HTML tags allowed for formatting</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-pink">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <li class="list-group-item text-muted">No borrowing steps yet.</li>
                        @endforelse
                    </ul>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Returning Steps</h6>
                    <form action="{{ route('library.content.book-borrowing.step.add') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="type" value="returning">
                        <div class="input-group">
                            <textarea name="step" class="form-control @error('step') is-invalid @enderror" placeholder="Enter a new returning step (HTML allowed)" rows="2" required maxlength="1000">{{ old('step') }}</textarea>
                            <button type="submit" class="btn btn-pink">Add Step</button>
                        </div>
                        <small class="text-muted">HTML tags allowed for formatting</small>
                        @error('step') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </form>

                    <ul class="list-group">
                        @forelse($bookBorrowingSettings->returning_steps ?? [] as $index => $step)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong>{{ $index + 1 }}.</strong> {!! $step !!}
                            </div>
                            <div class="btn-group btn-group-sm ms-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editReturningStepModal{{ $index }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('library.content.book-borrowing.step.delete') }}" method="POST" onsubmit="return confirm('Delete this step?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    <input type="hidden" name="type" value="returning">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editReturningStepModal{{ $index }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('library.content.book-borrowing.step.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <input type="hidden" name="type" value="returning">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Returning Step {{ $index + 1 }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea name="step" class="form-control" rows="3" required>{{ $step }}</textarea>
                                            <small class="text-muted">HTML tags allowed for formatting</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-pink">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <li class="list-group-item text-muted">No returning steps yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanning Service Management Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-pink fw-bold mb-1">Scanning Service Settings</h3>
                    <small class="text-muted">Manage Scanning Service page content and images</small>
                    
                    <form action="{{ route('library.content.scanning-service.update') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Page Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $scanningServiceSettings->title) }}" required maxlength="255">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Important Note</label>
                            <textarea name="important_note" class="form-control @error('important_note') is-invalid @enderror" rows="3" maxlength="2000">{{ old('important_note', $scanningServiceSettings->important_note) }}</textarea>
                            <small class="text-muted">HTML allowed (e.g., &lt;br&gt; for line breaks)</small>
                            @error('important_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Extract Limits</label>
                            <textarea name="extract_limits" class="form-control @error('extract_limits') is-invalid @enderror" rows="3" maxlength="2000">{{ old('extract_limits', $scanningServiceSettings->extract_limits) }}</textarea>
                            <small class="text-muted">HTML allowed (e.g., &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;)</small>
                            @error('extract_limits') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-pink">Update Settings</button>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Scanning Service Images (Slideshow)</h6>
                    <form action="{{ route('library.content.scanning-service.image.add') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                            <button type="submit" class="btn btn-pink">Add Image</button>
                        </div>
                        <small class="text-muted">Max 5 MB; JPG, PNG, or GIF</small>
                        @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </form>

                    <div class="row g-3">
                        @forelse($scanningServiceSettings->images ?? [] as $index => $imagePath)
                        <div class="col-md-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $imagePath) }}" class="card-img-top" alt="Scanning Service Image" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <form action="{{ route('library.content.scanning-service.image.delete') }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">No images uploaded yet.</div>
                        </div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Scanning Steps</h6>
                    <form action="{{ route('library.content.scanning-service.step.add') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <textarea name="step" class="form-control @error('step') is-invalid @enderror" placeholder="Enter a new scanning step (HTML allowed)" rows="2" required maxlength="1000">{{ old('step') }}</textarea>
                            <button type="submit" class="btn btn-pink">Add Step</button>
                        </div>
                        <small class="text-muted">HTML tags allowed for formatting</small>
                        @error('step') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </form>

                    <ul class="list-group">
                        @forelse($scanningServiceSettings->steps ?? [] as $index => $step)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong>{{ $index + 1 }}.</strong> {!! $step !!}
                            </div>
                            <div class="btn-group btn-group-sm ms-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editScanningStepModal{{ $index }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('library.content.scanning-service.step.delete') }}" method="POST" onsubmit="return confirm('Delete this step?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editScanningStepModal{{ $index }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('library.content.scanning-service.step.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Scanning Step {{ $index + 1 }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea name="step" class="form-control" rows="3" required>{{ $step }}</textarea>
                                            <small class="text-muted">HTML tags allowed for formatting</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-pink">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <li class="list-group-item text-muted">No scanning steps yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.contact-info.update') }}">
                        @csrf
                        @method('PUT')
                        <h3 class="text-pink fw-bold mb-1">Library Contact Information</h3>
                        <small class="text-muted d-block mb-4">Update library contact details and social media links</small>
                        
                        <h6 class="fw-semibold mb-3">Contact Numbers</h6>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">College Library</label>
                                <input type="text" name="phone_college" class="form-control" value="{{ $contact?->phone_college }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Graduate Library</label>
                                <input type="text" name="phone_graduate" class="form-control" value="{{ $contact?->phone_graduate }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Senior High School Library</label>
                                <input type="text" name="phone_senior_high" class="form-control" value="{{ $contact?->phone_senior_high }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IBED Library</label>
                                <input type="text" name="phone_ibed" class="form-control" value="{{ $contact?->phone_ibed }}">
                            </div>
                        </div>
                        
                        <h6 class="fw-semibold mb-3">Social Media & Online</h6>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Facebook URL</label>
                                <input type="text" name="facebook_url" class="form-control" value="{{ $contact?->facebook_url }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $contact?->email }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website URL</label>
                                <input type="text" name="website_url" class="form-control" value="{{ $contact?->website_url }}">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-pink px-4 py-2">Update Contact Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('management-scripts')
<script>
// Basic drag sort and send order to server
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helper function to show toast notifications
    function showToast(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 3000);
    }

    // Handle all form submissions with AJAX
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            // Skip forms with file uploads for now (handle separately)
            const hasFileUpload = form.querySelector('input[type="file"]');
            if (hasFileUpload && hasFileUpload.files.length > 0) {
                return; // Let it submit normally
            }

            e.preventDefault();
            
            const formData = new FormData(form);
            const method = formData.get('_method') || form.method;
            const action = form.action;

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showToast(data.message || 'Updated successfully!', 'success');
                    
                    // Reload page content without losing scroll position
                    const scrollPos = window.scrollY;
                    setTimeout(() => {
                        location.reload();
                        window.scrollTo(0, scrollPos);
                    }, 500);
                } else {
                    showToast(data.message || 'An error occurred', 'danger');
                }
            } catch (error) {
                showToast('An error occurred', 'danger');
            }
        });
    });

    // Announcements drag and drop
    const list = document.getElementById('ann-list');
    if (list) {
        let dragged;

        list.querySelectorAll('.ann-item').forEach(li => {
            li.setAttribute('draggable', 'true');
            li.addEventListener('dragstart', (e) => { dragged = li; li.classList.add('dragging'); });
            li.addEventListener('dragend', () => { dragged = null; li.classList.remove('dragging'); sendOrder(); });
            li.addEventListener('dragover', (e) => {
                e.preventDefault();
                const target = e.currentTarget;
                if (dragged && target !== dragged) {
                    const rect = target.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                    list.insertBefore(dragged, next ? target.nextSibling : target);
                }
            });
        });

        function sendOrder() {
            const ids = Array.from(list.querySelectorAll('.ann-item')).map(li => li.dataset.id);
            fetch("{{ route('library.content.announcements.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ order: ids })
            });
        }
    }

    // Slideshow images drag and drop
    const slideshowList = document.getElementById('slideshow-list');
    if (slideshowList) {
        let draggedSlide;

        slideshowList.querySelectorAll('.slideshow-item').forEach(item => {
            item.setAttribute('draggable', 'true');
            item.style.cursor = 'grab';
            
            item.addEventListener('dragstart', (e) => { 
                draggedSlide = item; 
                item.style.opacity = '0.5';
                item.style.cursor = 'grabbing';
            });
            
            item.addEventListener('dragend', () => { 
                draggedSlide = null; 
                item.style.opacity = '1';
                item.style.cursor = 'grab';
                sendSlideshowOrder(); 
            });
            
            item.addEventListener('dragover', (e) => {
                e.preventDefault();
                const target = e.currentTarget;
                if (draggedSlide && target !== draggedSlide) {
                    const rect = target.getBoundingClientRect();
                    const midpoint = rect.left + rect.width / 2;
                    const next = e.clientX > midpoint;
                    slideshowList.insertBefore(draggedSlide, next ? target.nextSibling : target);
                }
            });
        });

        function sendSlideshowOrder() {
            const ids = Array.from(slideshowList.querySelectorAll('.slideshow-item')).map(item => item.dataset.id);
            fetch("{{ route('library.content.slideshow.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ order: ids })
            });
        }
    }
});
</script>
@endpush
