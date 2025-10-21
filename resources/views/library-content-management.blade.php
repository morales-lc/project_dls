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
            <small class="text-muted">Manage Library Hours GIF and announcements</small>
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
                            <input type="file" name="gif" accept="image/gif" class="form-control" />
                            <small class="text-muted">Max 4 MB; GIF only.</small>
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
                            <input type="text" name="text" class="form-control" placeholder="Add an announcement..." required maxlength="500">
                            <button class="btn btn-outline-pink" type="submit">Add</button>
                        </div>
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
</div>
@endsection

@push('management-scripts')
<script>
// Basic drag sort and send order to server
document.addEventListener('DOMContentLoaded', function() {
    const list = document.getElementById('ann-list');
    if (!list) return;
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order: ids })
        });
    }
});
</script>
@endpush
