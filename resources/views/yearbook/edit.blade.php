@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title', 'Edit Yearbook')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 900px; background: #fff;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Edit Yearbook</h2>
            <a href="{{ route('yearbook.manage') }}" class="btn btn-secondary px-4">Cancel</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <h6 class="fw-bold"><i class="bi bi-exclamation-circle me-1"></i> Please fix the following errors:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('yearbook.update', $yearbook->id) }}" method="POST" enctype="multipart/form-data" class="card p-3 shadow rounded-4">
            @method('PUT')
            @include('yearbook._form')

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-pink px-4" type="submit"><i class="bi bi-check2-circle"></i> Update</button>
                <a href="{{ route('yearbook.manage') }}" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
