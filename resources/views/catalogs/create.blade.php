@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Add Catalog')

@section('content')
<div class="py-5">
    <div class="card shadow rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Add Catalog</h2>
            <a href="{{ route('catalogs.manage') }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('catalogs.store') }}" enctype="multipart/form-data">
            @csrf
            @include('catalogs.partials.form-fields')
            <button type="submit" class="btn btn-primary">Save Catalog</button>
        </form>
    </div>
</div>
@endsection
