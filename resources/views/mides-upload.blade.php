<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload MIDES Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div id="dashboardWrapper" class="d-flex position-relative">
    @include('components.admin-sidebar')
    <div class="flex-grow-1">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0 text-pink">Upload MIDES Document</h2>
                <a href="{{ route('mides.management') }}" class="btn btn-outline-secondary">&larr; Back to MIDES Management</a>
            </div>
            <div class="card p-4 shadow rounded-4" style="max-width:900px;margin:auto;">
    <form method="POST" action="{{ route('mides.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required onchange="toggleFields()">
                <option value="">-- Select Type --</option>
                @foreach($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="graduateCategory" style="display:none;">
            <label for="category" class="form-label">Graduate Theses Category</label>
            <select name="category" id="category" class="form-select">
                <option value="">-- Select Category --</option>
                @foreach($graduateCategories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="undergradProgram" style="display:none;">
            <label for="program" class="form-label">Undergraduate Program</label>
            <select name="program" id="program" class="form-select">
                <option value="">-- Select Program --</option>
                @foreach($undergradPrograms as $prog)
                    <option value="{{ $prog }}">{{ $prog }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="seniorHighProgram" style="display:none;">
            <label for="senior_high_program" class="form-label">Senior High School Program</label>
            <select name="program" id="senior_high_program" class="form-select">
                <option value="">-- Select Program --</option>
                @foreach($seniorHighPrograms as $sh)
                    <option value="{{ $sh }}">{{ $sh }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="author" class="form-label">Author</label>
            <input type="text" name="author" id="author" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" class="form-control" min="1900" max="{{ date('Y') }}" required>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Title of Thesis/Paper</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="pdf" class="form-label">PDF Document</label>
            <input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf" required>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-success">Upload</button>
        </div>
    </form>
            </div>
        </div>
    </div>
</div>
<script>
function toggleFields() {
    var type = document.getElementById('type').value;
    var categoryField = document.getElementById('category');
    var programField = document.getElementById('program');
    var gradCatDiv = document.getElementById('graduateCategory');
    var undergradProgDiv = document.getElementById('undergradProgram');
    var seniorHighProgDiv = document.getElementById('seniorHighProgram');
    var seniorHighProgField = document.getElementById('senior_high_program');

    if (type === 'Graduate Theses') {
        gradCatDiv.style.display = 'block';
    } else {
        gradCatDiv.style.display = 'none';
        if (categoryField) categoryField.value = '';
    }

    if (type === 'Undergraduate Baby Theses') {
        undergradProgDiv.style.display = 'block';
    } else {
        undergradProgDiv.style.display = 'none';
        if (programField) programField.value = '';
    }

    if (type === 'Senior High School Research Paper') {
        seniorHighProgDiv.style.display = 'block';
    } else {
        seniorHighProgDiv.style.display = 'none';
        if (seniorHighProgField) seniorHighProgField.value = '';
    }
}
document.getElementById('type').addEventListener('change', toggleFields);
document.addEventListener('DOMContentLoaded', toggleFields);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
