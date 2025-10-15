@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Feedback Submissions')

@section('content')


<style>
    /* Pink button (View Message) */
    .btn-pink {
        background-color: #e83e8c;
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-pink:hover {
        background-color: #d63384;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Solid Red button (Delete) */
    .btn-red {
        background-color: #dc3545;
        /* Bootstrap danger red */
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-red:hover {
        background-color: #bb2d3b;
        color: #fff;
        transform: translateY(-2px);
    }
</style>
<div class="py-5">
    <div class="card shadow rounded-4 w-100" style="max-width:1100px;margin:auto; background:#fff;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0 text-pink">Feedback Submissions</h2>
                <div>
                    <a href="{{ route('feedback.admin') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </div>

            <!-- Filter & Search Card -->
            <div class="card p-3 mb-3 shadow-sm rounded-3">
                <form method="GET" action="{{ route('feedback.admin') }}" class="row g-2 mb-0 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="user" class="form-control" placeholder="Search user name/email" value="{{ request('user') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="course" class="form-control" placeholder="Course" value="{{ request('course') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="role" class="form-control" placeholder="Role" value="{{ request('role') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
                    </div>
                    <div class="col-md-2 d-none d-md-block">
                        <a href="{{ route('feedback.admin') }}" class="btn btn-pink w-100">Clear</a>
                    </div>
                </form>
            </div>

            <div class="card p-3 shadow-sm rounded-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                        <thead class="table-pink">
                            <tr>
                                <th>User</th>
                                <th>Course</th>
                                <th>Role</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbacks as $feedback)
                            <tr>
                                <td>
                                    @if($feedback->user)
                                    {{ $feedback->user->name }}<br>
                                    <small class="text-muted">{{ $feedback->user->email }}</small>
                                    @else
                                    <span class="text-muted">Anonymous</span>
                                    @endif
                                </td>
                                <td>{{ $feedback->course ?? '-' }}</td>
                                <td>{{ ucfirst($feedback->role ?? '-') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-pink" data-bs-toggle="modal" data-bs-target="#messageModal{{ $feedback->id }}">
                                        View Message
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="messageModal{{ $feedback->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $feedback->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="messageModalLabel{{ $feedback->id }}">Feedback Message</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="white-space:pre-line;">
                                                    {{ $feedback->message }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $feedback->created_at->format('Y-m-d H:i') }}</td>
                                <td class="d-flex gap-1">
                                    <form method="POST" action="{{ route('feedback.delete', $feedback->id) }}" onsubmit="return confirm('Delete this feedback?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-red">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No feedback found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $feedbacks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection