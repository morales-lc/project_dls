@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    /* ===== Hover pop-out effect for appointment rows ===== */
    table.table tr.record-row {
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            background-color 0.2s ease;
    }

    table.table tr.record-row:hover {
        background-color: #fff6f9;
        /* soft pink tint */
        transform: scale(1.02);
        /* slight pop-out */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        /* floating look */
        position: relative;
        z-index: 5;
        /* ensures hover row appears above others */
    }

    /* Keep cell background consistent on hover */
    table.table tr.record-row:hover>td {
        background-color: transparent;
    }

    /* Cursor & selection behavior */
    table.table tr.record-row td {
        cursor: pointer;
    }

    table.table tr.record-row td:last-child {
        cursor: default;
    }
</style>
@endpush
@section('title','ALINET Appointments Management')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1400px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 1.75rem;">ALINET Appointments Management</h2>

        </div>

        {{-- Status Tabs --}}
        @php
            $activeStatus = $status ?? request('status');
            $preserve = request()->except(['page','status']);
        @endphp
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ empty($activeStatus) ? 'active' : '' }}" href="{{ route('alinet.manage', $preserve) }}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeStatus === 'pending' ? 'active' : '' }}" href="{{ route('alinet.manage', array_merge($preserve, ['status' => 'pending'])) }}">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeStatus === 'accepted' ? 'active' : '' }}" href="{{ route('alinet.manage', array_merge($preserve, ['status' => 'accepted'])) }}">Accepted</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeStatus === 'rejected' ? 'active' : '' }}" href="{{ route('alinet.manage', array_merge($preserve, ['status' => 'rejected'])) }}">Rejected</a>
            </li>
        </ul>

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
                            <th style="min-width: 220px;">Name</th>
                            <th style="min-width: 200px;">Email</th>
                            <th style="min-width: 140px;">Date</th>
                            <th style="min-width: 180px;">Mode</th>
                            <th style="min-width: 110px;">Status</th>
                            <th style="min-width: 160px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $i => $a)
                        @php
                        $dateText = !empty($a->appointment_date)
                        ? \Carbon\Carbon::parse($a->appointment_date)->format('F j, Y')
                        : '—';
                        @endphp
                        <tr class="record-row" style="height: 48px; cursor: pointer;" title="Click to view details"
                            data-appointment="true"
                            data-id="{{ $a->id }}"
                            data-prefix="{{ e($a->prefix) }}"
                            data-firstname="{{ e($a->firstname) }}"
                            data-lastname="{{ e($a->lastname) }}"
                            data-email="{{ e($a->email) }}"
                            data-strand="{{ e($a->strand_course) }}"
                            data-institution="{{ e($a->institution_college) }}"
                            data-titles="{{ e($a->titles_or_topics) }}"
                            data-date="{{ e($a->appointment_date) }}"
                            data-date-text="{{ $dateText }}"
                            data-mode="{{ e($a->mode_of_research) }}"
                            data-assistance='@json($a->assistance)'
                            data-resource-types='@json($a->resource_types)'
                            data-status="{{ e($a->status) }}"
                            data-created="{{ optional($a->created_at)->format('F j, Y g:i A') }}">
                            <td class="text-start">
                                {{ $a->prefix ? $a->prefix . ' ' : '' }}{{ $a->firstname }} {{ $a->lastname }}
                            </td>
                            <td>{{ $a->email }}</td>
                            <td>{{ $dateText }}</td>
                            <td>{{ $a->mode_of_research }}</td>
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
                                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                    <button class="btn btn-success btn-sm mb-1 no-row-modal" data-no-row-click="true" type="submit">Accept</button>
                                </form>
                                <button class="btn btn-danger btn-sm mb-1 no-row-modal" data-no-row-click="true" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal" data-action="{{ route('alinet.status', $a->id) }}" data-name="{{ $a->firstname }} {{ $a->lastname }}">Reject</button>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No appointments found.</td>
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
                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#fce1ea; border-bottom: 1px solid #f5b1c9;">
                <div>
                    <h5 class="modal-title fw-bold" id="detailsTitle" style="color:#d81b60;">Appointment Details</h5>
                    <div class="small text-muted" id="detailsSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100 border-0" style="background:#fff7fa;">
                            <div class="card-body py-3">
                                <h6 class="text-uppercase text-muted mb-3" style="letter-spacing: .08em;">Requester</h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-5 text-muted">Full name</dt>
                                    <dd class="col-7" id="d_name">—</dd>
                                    <dt class="col-5 text-muted">Email</dt>
                                    <dd class="col-7" id="d_email">—</dd>
                                    <dt class="col-5 text-muted">Strand/Course</dt>
                                    <dd class="col-7" id="d_strand">—</dd>
                                    <dt class="col-5 text-muted">Institution/College</dt>
                                    <dd class="col-7" id="d_institution">—</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0" style="background:#fff7fa;">
                            <div class="card-body py-3">
                                <h6 class="text-uppercase text-muted mb-3" style="letter-spacing: .08em;">Appointment</h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-5 text-muted">Mode</dt>
                                    <dd class="col-7" id="d_mode">—</dd>
                                    <dt class="col-5 text-muted">Preferred/Set Date</dt>
                                    <dd class="col-7" id="d_date">—</dd>
                                    <dt class="col-5 text-muted">Status</dt>
                                    <dd class="col-7" id="d_status">—</dd>
                                    <dt class="col-5 text-muted">Submitted</dt>
                                    <dd class="col-7" id="d_created">—</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0" style="background:#fff;">
                            <div class="card-body py-3">
                                <h6 class="text-uppercase text-muted mb-3" style="letter-spacing: .08em;">Research Need</h6>
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">Titles/Topics</div>
                                    <div id="d_titles" class="border rounded p-2 bg-light" style="white-space: pre-wrap;">—</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Assistance</div>
                                        <div id="d_assistance" class="d-flex flex-wrap gap-1"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Resource Types</div>
                                        <div id="d_resources" class="d-flex flex-wrap gap-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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

        // Row click to open details modal
        const detailsModalEl = document.getElementById('detailsModal');
        const detailsModal = detailsModalEl ? new bootstrap.Modal(detailsModalEl) : null;
        const byId = (id) => document.getElementById(id);

        function renderChips(container, items) {
            container.innerHTML = '';
            if (!items || (Array.isArray(items) && items.length === 0)) {
                container.innerHTML = '<span class="text-muted">—</span>';
                return;
            }
            try {
                if (typeof items === 'string') {
                    items = JSON.parse(items);
                }
            } catch (e) {}
            (items || []).forEach(function(txt) {
                if (txt === null || txt === undefined || txt === '') return;
                const span = document.createElement('span');
                span.className = 'badge text-dark';
                span.style.background = '#fcb6d0';
                span.style.color = '#d81b60';
                span.textContent = txt;
                container.appendChild(span);
            });
            if (!container.childElementCount) {
                container.innerHTML = '<span class="text-muted">—</span>';
            }
        }

        document.querySelectorAll('tr.record-row').forEach(function(row) {
            row.addEventListener('click', function(ev) {
                // Prevent triggering when clicking buttons/links inside the row
                const target = ev.target;
                if (target.closest('[data-no-row-click="true"],button,a,form,input,select,textarea')) return;
                if (!detailsModal) return;

                const prefix = row.getAttribute('data-prefix') || '';
                const firstname = row.getAttribute('data-firstname') || '';
                const lastname = row.getAttribute('data-lastname') || '';
                const name = (prefix ? prefix + ' ' : '') + firstname + ' ' + lastname;
                const email = row.getAttribute('data-email') || '—';
                const strand = row.getAttribute('data-strand') || '—';
                const institution = row.getAttribute('data-institution') || '—';
                const titles = row.getAttribute('data-titles') || '—';
                const dateText = row.getAttribute('data-date-text') || '—';
                const mode = row.getAttribute('data-mode') || '—';
                const status = row.getAttribute('data-status') || '—';
                const created = row.getAttribute('data-created') || '—';
                const assistance = row.getAttribute('data-assistance');
                const resourceTypes = row.getAttribute('data-resource-types');

                byId('detailsTitle').textContent = name;
                byId('detailsSubtitle').textContent = email;
                byId('d_name').textContent = name;
                byId('d_email').textContent = email;
                byId('d_strand').textContent = strand || '—';
                byId('d_institution').textContent = institution || '—';
                byId('d_mode').textContent = mode;
                byId('d_date').textContent = dateText;
                byId('d_status').textContent = status.charAt(0).toUpperCase() + status.slice(1);
                byId('d_created').textContent = created;
                byId('d_titles').textContent = titles;

                renderChips(byId('d_assistance'), assistance ? JSON.parse(assistance) : []);
                renderChips(byId('d_resources'), resourceTypes ? JSON.parse(resourceTypes) : []);

                detailsModal.show();
            });
        });
    });
</script>
@endpush
@endsection