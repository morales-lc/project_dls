@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title','Manage Sidlak Journals')

@section('content')
    <div class="py-5">
        <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold mb-0 text-pink">Manage Sidlak Journals</h2>
                    <a href="{{ route('sidlak.create') }}" class="btn btn-pink px-4"><i class="bi bi-plus-lg"></i> Add Journal</a>
                </div>
                @if(session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
                @endif
                <div class="card p-3 shadow rounded-4 border-0">
                    <form method="GET" action="{{ route('sidlak.manage') }}" class="row g-2 mb-3 align-items-end">
                        <div class="col-auto">
                            <label for="year" class="form-label small mb-1">Year</label>
                            <select name="year" id="year" class="form-select form-select-sm">
                                <option value="">All years</option>
                                @foreach($years ?? [] as $yr)
                                    <option value="{{ $yr }}" @if(isset($selectedYear) && $selectedYear == $yr) selected @endif>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="month" class="form-label small mb-1">Month</label>
                            <select name="month" id="month" class="form-select form-select-sm">
                                <option value="">All months</option>
                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                    <option value="{{ $m }}" @if(isset($selectedMonth) && $selectedMonth == $m) selected @endif>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="q" class="form-label small mb-1">Title contains</label>
                            <input type="search" name="q" id="q" value="{{ $q ?? '' }}" class="form-control form-control-sm" placeholder="Search title...">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-pink btn-sm px-3">Filter</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('sidlak.manage') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                            <thead class="table-pink">
                                <tr>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>ISSN</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journals as $journal)
                                <tr>
                                    <td style="width: 80px;">
                                        @if($journal->cover_photo)
                                        <img src="{{ asset('storage/' . $journal->cover_photo) }}" alt="Cover" class="img-thumbnail rounded-3 shadow-sm" style="width:60px; height:80px; object-fit:cover; background:#fff;">
                                        @else
                                        <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center" style="width:60px; height:80px;">
                                            <i class="bi bi-image text-secondary fs-3"></i>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $journal->title }}</td>
                                    <td>{{ $journal->month }}</td>
                                    <td>{{ $journal->year }}</td>
                                    <td>{{ $journal->print_issn }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('sidlak.edit', $journal->id) }}" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                        <form method="POST" action="{{ route('sidlak.destroy', $journal->id) }}" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this journal? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- Icons and scripts are provided by layouts.management (management-head / management-scripts) --}}
        </div>
    </div>
@endsection