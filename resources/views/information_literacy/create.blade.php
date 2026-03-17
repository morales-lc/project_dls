@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
@endpush

@section('title', 'Information Literacy - Create')

@section('content')
    <div class="py-5 d-flex flex-column align-items-center justify-content-center">
        <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 800px; background: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('information_literacy.manage') }}" class="btn btn-outline-secondary px-4 py-2">
                    &larr; Back to Manage
                </a>
                <span></span>
            </div>

            <h2 class="fw-bold mb-4 text-center" 
                style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">
                Post Information Literacy Seminar
            </h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('information_literacy.store') }}" enctype="multipart/form-data" class="row g-4">
                @csrf

                <div class="col-12">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control form-control-lg @error('description') is-invalid @enderror" rows="4" maxlength="5000" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_time" class="form-control form-control-lg @error('date_time') is-invalid @enderror" value="{{ old('date_time') }}" min="{{ date('Y-m-d\TH:i') }}" required>
                    @error('date_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="onsite" @if(old('type')=='onsite') selected @endif>Onsite</option>
                        <option value="online" @if(old('type')=='online') selected @endif>Online</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Facilitator/s <span class="text-danger">*</span></label>
                    <input type="text" name="facilitators" class="form-control form-control-lg @error('facilitators') is-invalid @enderror" value="{{ old('facilitators') }}" maxlength="500" required>
                    @error('facilitators')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Image (Max: 5MB, optional)</label>
                    <input type="file" name="image" class="form-control form-control-lg @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                    <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex justify-content-center mt-2">
                    <button class="btn btn-lg px-5 py-2" type="submit" 
                        style="font-size:1.1rem; font-weight:600; background:#d81b60; color:#fff; border:none; border-radius:2em;">
                        Post Seminar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
