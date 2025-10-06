@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Edit Information Literacy Seminar')

@section('content')
    <div class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Edit Information Literacy Seminar</h2>
            <form method="POST" action="{{ route('information_literacy.update', $post->id) }}" enctype="multipart/form-data" class="card p-4 rounded-4 shadow-lg mx-auto" style="max-width:600px;">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ $post->title }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required>{{ $post->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="date_time" class="form-label">Date & Time</label>
                    <input type="datetime-local" name="date_time" id="date_time" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($post->date_time)) }}" required>
                </div>
                <div class="mb-3">
                    <label for="facilitators" class="form-label">Facilitator/s</label>
                    <input type="text" name="facilitators" id="facilitators" class="form-control" value="{{ $post->facilitators }}" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="onsite" @if($post->type=='onsite') selected @endif>Onsite</option>
                        <option value="online" @if($post->type=='online') selected @endif>Online</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    @if($post->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Current Image" style="max-width:180px;max-height:120px;" class="rounded shadow">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Update Seminar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
