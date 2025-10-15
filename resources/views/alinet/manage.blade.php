@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
@section('title','ALINET Appointments Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1400px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 1.75rem;">ALINET Appointments Management</h2>

        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-3 mb-3 shadow-sm rounded-3">
            <form method="GET" action="{{ route('alinet.manage') }}" class="mb-0">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label mb-1">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Name, email, strand, institution">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ request('status')==='accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label mb-1">Service</label>
                        <input type="text" name="service" value="{{ request('service') }}" class="form-control form-control-sm" placeholder="Service name">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label mb-1">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label mb-1">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-1">
                        <label class="form-label mb-1">Per page</label>
                        <select name="per_page" class="form-select form-select-sm">
                            @foreach([10,15,25,50,100] as $n)
                            <option value="{{ $n }}" {{ (int)request('per_page',15)===$n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="d-flex gap-2">
                            <button class="btn btn-dark" type="submit">Filter</button>
                            <a href="{{ route('alinet.manage') }}" class="btn btn-pink">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card p-3 shadow-sm rounded-3">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center" style="font-size: 0.9rem;">
                    <thead class="table-pink" style="background:#fcb6d0; color:#d81b60;">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Strand/Course</th>
                            <th>Institution/College</th>
                            <th>Date</th>
                            <th>Services</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $i => $a)
                        <tr style="height: 48px;">
                            <td>{{ $appointments->firstItem() + $i }}</td>
                            <td>{{ $a->prefix ? $a->prefix . ' ' : '' }}{{ $a->firstname }} {{ $a->lastname }}</td>
                            <td>{{ $a->email }}</td>
                            <td>{{ $a->strand_course }}</td>
                            <td>{{ $a->institution_college }}</td>
                            <td>{{ $a->appointment_date->format('Y-m-d') }}</td>
                            <td>
                                @foreach($a->services as $s)
                                <span class="badge bg-pink text-dark mb-1" style="background:#fcb6d0; color:#d81b60;">{{ $s }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($a->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($a->status === 'accepted')
                                <span class="badge bg-success">Accepted</span>
                                @else
                                <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($a->status === 'pending')
                                <form method="POST" action="{{ route('alinet.status', $a->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="accepted">
                                    <button class="btn btn-success btn-sm mb-1" type="submit">Accept</button>
                                </form>
                                <button class="btn btn-danger btn-sm mb-1" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal" data-action="{{ route('alinet.status', $a->id) }}" data-name="{{ $a->firstname }} {{ $a->lastname }}">Reject</button>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No appointments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2" style="gap: 8px;">
                <div class="small text-muted">
                    Showing {{ $appointments->firstItem() ?? 0 }}–{{ $appointments->lastItem() ?? 0 }} of {{ $appointments->total() }}
                </div>
                <div>
                    {{ $appointments->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="rejectModalForm">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <div class="mb-2 text-muted" id="rejectModalName"></div>
                    <label class="form-label">Reason (optional)</label>
                    <textarea name="reason" class="form-control" rows="4" placeholder="Provide a brief explanation"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Send Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('management-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var rejectModal = document.getElementById('rejectModal');
        rejectModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var action = button.getAttribute('data-action');
            var name = button.getAttribute('data-name');
            var form = document.getElementById('rejectModalForm');
            var nameEl = document.getElementById('rejectModalName');
            form.setAttribute('action', action);
            nameEl.textContent = 'Rejecting: ' + name;
            form.querySelector('textarea[name="reason"]').value = '';
        });
    });
</script>
@endpush
@endsection