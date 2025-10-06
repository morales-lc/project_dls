@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Manage Contact Info')

@section('content')
    <div class="container py-5">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h2 class="fw-bold text-pink mb-0">Manage Contact Info</h2>
                <small class="text-muted">Updated {{ now()->format('M d, Y h:i A') }}</small>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow p-5 mb-5" style="border: 2.5px solid #d81b60; background: #fff6fa;">
                    <form method="POST" action="{{ route('admin.contact-info.update') }}">
                        @csrf
                        @method('PUT')
                        <h4 class="fw-bold text-pink mb-4">Library Contact Numbers</h4>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">College Library</label>
                                <input type="text" name="phone_college" class="form-control" value="{{ $contact?->phone_college }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Graduate Library</label>
                                <input type="text" name="phone_graduate" class="form-control" value="{{ $contact?->phone_graduate }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Senior High School Library</label>
                                <input type="text" name="phone_senior_high" class="form-control" value="{{ $contact?->phone_senior_high }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">IBED Library</label>
                                <input type="text" name="phone_ibed" class="form-control" value="{{ $contact?->phone_ibed }}">
                            </div>
                        </div>
                        <h4 class="fw-bold text-pink mb-4 mt-4">Social Media & Online</h4>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Facebook URL</label>
                                <input type="text" name="facebook_url" class="form-control" value="{{ $contact?->facebook_url }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $contact?->email }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Website URL</label>
                                <input type="text" name="website_url" class="form-control" value="{{ $contact?->website_url }}">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-pink px-4 py-2">Update Contact Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection