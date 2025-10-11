@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Upload MIDES Document')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 800px; background: #fff;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('mides.management') }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to MIDES Management</a>
            <span></span>
        </div>

        <h2 class="fw-bold mb-4 text-center" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Upload MIDES Document</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('mides.store') }}" enctype="multipart/form-data" class="row g-4">
            @csrf

            <div class="col-12">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-select form-select-lg" required onchange="toggleFields()">
                    <option value="">-- Select Type --</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Graduate --}}
            <div class="col-12" id="graduateCategory" style="display:none;">
                <label class="form-label">Graduate Theses Category</label>
                <select name="mides_category_id" id="mides_category_id" class="form-select form-select-lg">
                    <option value="">-- Select Category --</option>
                    @foreach($graduateCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Undergraduate --}}
            <div class="col-12" id="undergradProgram" style="display:none;">
                <label class="form-label">Undergraduate Program</label>
                <select name="mides_category_id" id="mides_category_id_undergrad" class="form-select form-select-lg">
                    <option value="">-- Select Program --</option>
                    @foreach($undergradPrograms as $prog)
                        <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Senior High --}}
            <div class="col-12" id="seniorHighProgram" style="display:none;">
                <label class="form-label">Senior High School Program</label>
                <select name="mides_category_id" id="mides_category_id_sh" class="form-select form-select-lg">
                    <option value="">-- Select Program --</option>
                    @foreach($seniorHighPrograms as $sh)
                        <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Author <span class="text-danger">*</span></label>
                <input type="text" name="author" id="author" class="form-control form-control-lg" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <input type="number" name="year" id="year" class="form-control form-control-lg" min="1900" max="{{ date('Y') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Title of Thesis/Paper <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control form-control-lg" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">PDF Document <span class="text-danger">*</span></label>
                <input type="file" name="pdf" id="pdf" class="form-control form-control-lg" accept="application/pdf" required>
            </div>
            <div style="height: 20px;"></div>
            <div class="col-md d-flex align-items-end justify-content-center mt-2">
                <button class="btn btn-lg px-5 py-2" type="submit" style="font-size:1.1rem; font-weight:600; background:#d81b60; color:#fff; border:none; border-radius:2em;">
                    Upload Document
                </button>
            </div>
        </form>
    </div>
</div>

@push('management-scripts')
<script>
function toggleFields() {
    var type = document.getElementById('type').value;
    var gradCatDiv = document.getElementById('graduateCategory');
    var undergradProgDiv = document.getElementById('undergradProgram');
    var seniorHighProgDiv = document.getElementById('seniorHighProgram');

    if (type === 'Graduate Theses') {
        gradCatDiv.style.display = 'block';
        document.getElementById('mides_category_id').disabled = false;
        document.getElementById('mides_category_id_undergrad').disabled = true;
        document.getElementById('mides_category_id_sh').disabled = true;
    } else {
        gradCatDiv.style.display = 'none';
        document.getElementById('mides_category_id').disabled = true;
    }

    if (type === 'Undergraduate Baby Theses') {
        undergradProgDiv.style.display = 'block';
        document.getElementById('mides_category_id_undergrad').disabled = false;
        document.getElementById('mides_category_id').disabled = true;
        document.getElementById('mides_category_id_sh').disabled = true;
    } else {
        undergradProgDiv.style.display = 'none';
        document.getElementById('mides_category_id_undergrad').disabled = true;
    }

    if (type === 'Senior High School Research Paper') {
        seniorHighProgDiv.style.display = 'block';
        document.getElementById('mides_category_id_sh').disabled = false;
        document.getElementById('mides_category_id').disabled = true;
        document.getElementById('mides_category_id_undergrad').disabled = true;
    } else {
        seniorHighProgDiv.style.display = 'none';
        document.getElementById('mides_category_id_sh').disabled = true;
    }
}
document.addEventListener('DOMContentLoaded', toggleFields);
document.getElementById('type').addEventListener('change', toggleFields);
</script>
@endpush
@endsection
