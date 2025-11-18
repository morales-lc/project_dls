
@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title', 'Alert Services - Create')

@section('content')
    <div class="py-5 d-flex flex-column align-items-center justify-content-center">
        <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 800px; background: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ request('return', route('alert-services.manage')) }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Manage</a>
                <span></span>
            </div>
            <h2 class="fw-bold mb-4 text-center" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Post New Book</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('alert-services.store') }}" enctype="multipart/form-data" class="row g-4">
                @csrf
                <input type="hidden" name="return_url" value="{{ request('return', url()->previous()) }}">
                <div class="col-12">
                    <label class="form-label">Title (optional)</label>
                    <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Call Number (optional)</label>
                    <input type="text" name="call_number" class="form-control form-control-lg" value="{{ old('call_number') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Author (optional)</label>
                    <input type="text" name="author" class="form-control form-control-lg" value="{{ old('author') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select form-select-lg" required>
                        <option value="">Select Month</option>
                        @for($m=1;$m<=12;$m++)
                            <option value="{{ $m }}" @if(old('month')==$m) selected @endif>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control form-control-lg" min="2000" max="2100" value="{{ old('year', date('Y')) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Department / Special Category <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select form-select-lg" required>
                        <option value="">Select Department or Category</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" @if(old('department_id')==$dept->id) selected @endif>{{ $dept->name }} ({{ ucfirst($dept->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PDF File <span class="text-danger">*</span></label>
                    <input type="file" name="pdf_file" class="form-control form-control-lg" accept="application/pdf" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cover Image (optional)</label>
                    <input type="file" name="cover_image" class="form-control form-control-lg" accept="image/*">
                </div>
                <div class="col-12 d-flex justify-content-center mt-2 gap-2">
                    <button class="btn btn-lg px-5 py-2" type="submit" style="font-size:1.1rem; font-weight:600; background:#d81b60; color:#fff; border:none; border-radius:2em;">Post Book</button>
                    <a href="{{ request('return', route('alert-services.manage')) }}" class="btn btn-lg px-4 py-2" style="background:#bdbdbd; color:#222; border:none; border-radius:2em; font-weight:600;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

