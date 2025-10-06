@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title','Librarian Dashboard')

@section('content')
    <div class="container-fluid py-5">
        <div class="card shadow rounded-4 p-4">
            <h3 class="fw-bold">Welcome, {{ auth()->user()->name ?? 'Librarian' }}</h3>
            <p class="text-muted">Use the sidebar to access management modules.</p>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Information Literacy</h5>
                            <p class="card-text">Create and manage seminars and events.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('information_literacy.index') }}" class="btn btn-outline-secondary btn-sm">View</a>
                                <a href="{{ route('information_literacy.manage') }}" class="btn btn-primary btn-sm">Manage</a>
                                <a href="{{ route('information_literacy.create') }}" class="btn btn-success btn-sm">Create</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Posts & Announcements</h5>
                            <p class="card-text">Manage dashboard posts and announcements.</p>
                            <a href="{{ route('post.management') }}" class="btn btn-primary btn-sm">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Sidlak Journal</h5>
                            <p class="card-text">Manage Sidlak journal entries.</p>
                            <a href="{{ route('sidlak.index') }}" class="btn btn-primary btn-sm">Manage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('management-scripts')
@endpush
