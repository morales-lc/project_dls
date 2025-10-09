@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Alert Services - Edit')

@section('content')
    <div class="py-5 d-flex flex-column align-items-center justify-content-center">
        <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 800px; background: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('alert-services.manage') }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to Manage</a>
                <a href="{{ route('alert-services.create') }}" class="btn btn-outline-primary px-4 py-2">+ Add New Book</a>
            </div>

            <h2 class="fw-bold mb-4 text-center" style="letter-spacing: 1px; color: #1976d2; font-size: 2rem;">Edit Alert Book</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('alert-services.update', $book->id) }}" enctype="multipart/form-data" class="row g-4">
                @csrf
                @method('PUT')

                <div class="col-12 d-flex flex-column align-items-center mb-2">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Cover" style="width:160px;height:230px;object-fit:cover;" class="mb-3 rounded-3 shadow">
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">Title (optional)</label>
                    <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $book->title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select form-select-lg" required>
                        <option value="">Select Month</option>
                        @for($m=1;$m<=12;$m++)
                            <option value="{{ $m }}" @if(old('month', $book->month)==$m) selected @endif>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control form-control-lg" min="2000" max="2100"
                        value="{{ old('year', $book->year) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Department / Special Category <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select form-select-lg" required>
                        <option value="">Select Department or Category</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" @if(old('department_id', $book->department_id)==$dept->id) selected @endif>
                                {{ $dept->name }} ({{ ucfirst($dept->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">PDF File (leave blank to keep current)</label>
                    <input type="file" name="pdf_file" class="form-control form-control-lg" accept="application/pdf">
                    @if($book->pdf_path)
                        <a href="{{ asset('storage/'.$book->pdf_path) }}" target="_blank" class="d-block mt-1 text-decoration-none">Current PDF</a>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cover Image (leave blank to keep current)</label>
                    <input type="file" name="cover_image" class="form-control form-control-lg" accept="image/*">
                </div>

                <div class="col-12 d-flex justify-content-center mt-2 gap-2">
                    <button class="btn btn-lg px-5 py-2" type="submit"
                        style="font-size:1.1rem; font-weight:600; background:#1976d2; color:#fff; border:none; border-radius:2em;">
                        Update Book
                    </button>
                    <a href="{{ route('alert-services.manage') }}" class="btn btn-lg px-4 py-2"
                        style="background:#bdbdbd; color:#222; border:none; border-radius:2em; font-weight:600;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
