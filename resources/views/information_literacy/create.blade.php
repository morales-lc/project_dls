@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Post Information Literacy Seminar')

@section('content')
    <div class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Post Information Literacy Seminar</h2>
            <form method="POST" action="{{ route('information_literacy.store') }}" enctype="multipart/form-data" class="card p-4 rounded-4 shadow-lg mx-auto" style="max-width:600px;">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="date_time" class="form-label">Date & Time</label>
                    <input type="datetime-local" name="date_time" id="date_time" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="facilitators" class="form-label">Facilitator/s</label>
                    <input type="text" name="facilitators" id="facilitators" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="onsite">Onsite</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Post Seminar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
