

@include('navbar')
<div class="container py-4">
    <div class="card shadow rounded-4 p-4 w-100" style="max-width: 1400px; margin:auto; background: #fff;">
        <h2 class="fw-bold mb-4 text-center" style="color: #1976d2;">ALINET Appointments Management</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form method="GET" action="{{ route('alinet.manage') }}" class="mb-3">
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
                    <button class="btn btn-primary btn-sm mt-2 mt-md-0" type="submit">Filter</button>
                    <a href="{{ route('alinet.manage') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">Reset</a>
                </div>
            </div>
        </form>
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
                            <form method="POST" action="{{ route('alinet.status', $a->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-danger btn-sm mb-1" type="submit">Reject</button>
                            </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No appointments found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2" style="gap: 8px;">
            <div class="small text-muted">
                Showing {{ $appointments->firstItem() ?? 0 }}–{{ $appointments->lastItem() ?? 0 }} of {{ $appointments->total() }}
            </div>
            <!-- Pagination links removed as requested -->
        </div>
    </div>
</div>

@include('footer')