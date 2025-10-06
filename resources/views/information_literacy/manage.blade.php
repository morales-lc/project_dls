@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Manage Information Literacy Seminars')

@section('content')
    <div class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Manage Information Literacy Seminars</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="mb-4 text-end">
                <a href="{{ route('information_literacy.create') }}" class="btn btn-pink"><i class="bi bi-plus-lg"></i> Add New Seminar</a>
                <a href="{{ route('information_literacy.index') }}" class="btn btn-outline-secondary ms-2">View Seminars</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date & Time</th>
                            <th>Facilitator/s</th>
                            <th>Type</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->date_time }}</td>
                            <td>{{ $post->facilitators }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($post->type) }}</span></td>
                            <td>
                                @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" alt="Image" style="max-width:80px;max-height:60px;" class="rounded shadow">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('information_literacy.edit', $post->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('information_literacy.delete', $post->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this seminar?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No seminars found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
