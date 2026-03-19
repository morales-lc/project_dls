@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Feedback Submissions')

@section('content')


<style>
    :root {
        --fb-pink: #e83e8c;
        --fb-pink-deep: #c2185b;
        --fb-surface: #ffffff;
        --fb-line: #f0d9e4;
        --fb-muted: #6b7280;
    }

    .feedback-admin-shell {
        max-width: 1260px;
        margin: auto;
    }

    .feedback-admin-main {
        background: linear-gradient(180deg, #fff 0%, #fff9fc 100%);
        border: 1px solid var(--fb-line);
    }

    .feedback-admin-title {
        color: var(--fb-pink-deep);
        letter-spacing: 0.2px;
    }

    .filter-card {
        border: 1px solid #edd5e2;
        background: #fff;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 0.65rem;
    }

    .filter-col-3 {
        grid-column: span 3;
    }

    .filter-col-2 {
        grid-column: span 2;
    }

    .filter-col-1 {
        grid-column: span 1;
    }

    .table-shell {
        border: 1px solid #edd5e2;
    }

    .table thead th {
        border-bottom-width: 1px;
        white-space: nowrap;
        color: #3b1f30;
        font-size: 0.9rem;
    }

    .topic-link {
        color: #1e4ed8;
        font-weight: 700;
        text-decoration: none;
    }

    .topic-link:hover {
        color: #153ba1;
        text-decoration: underline;
    }

    .topic-snippet {
        color: var(--fb-muted);
        font-size: 0.94rem;
        max-width: 290px;
    }

    .actions-cell {
        min-width: 240px;
    }

    .status-editor {
        display: flex;
        gap: 0.4rem;
        margin-bottom: 0.35rem;
    }

    .status-editor .form-select {
        min-width: 116px;
    }

    .status-editor .btn {
        white-space: nowrap;
    }

    /* Pink button */
    .btn-pink {
        background-color: var(--fb-pink);
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

    .status-chip {
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.28rem 0.56rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .status-open {
        background: #e6f7ed;
        color: #137a40;
    }

    .status-resolved {
        background: #eaf1ff;
        color: #1d4bb8;
    }

    .status-closed {
        background: #f4f4f5;
        color: #575a62;
    }

    .category-chip {
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.25rem 0.55rem;
        background: rgba(216, 27, 96, 0.12);
        color: #a91549;
        letter-spacing: 0.03em;
    }

    @media (max-width: 991.98px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-col-3,
        .filter-col-2,
        .filter-col-1 {
            grid-column: span 2;
        }

        .actions-cell {
            min-width: 200px;
        }
    }
</style>
<div class="py-5">
    <div class="feedback-admin-shell card shadow rounded-4 w-100 feedback-admin-main">
        <div class="card-body p-4 p-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0 feedback-admin-title">Feedback Submissions</h2>
                <div>
                    <a href="{{ route('feedback.admin') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </div>

            <!-- Filter & Search Card -->
            <div class="card p-3 mb-3 shadow-sm rounded-3 filter-card">
                <form method="GET" action="{{ route('feedback.admin') }}" class="mb-0">
                    <div class="filter-grid">
                    <div class="filter-col-3">
                        <input type="text" name="user" class="form-control" placeholder="Search user name/email" value="{{ request('user') }}">
                    </div>
                    <div class="filter-col-2">
                        <input type="text" name="course" class="form-control" placeholder="Course" value="{{ request('course') }}">
                    </div>
                    <div class="filter-col-2">
                        <input type="text" name="role" class="form-control" placeholder="Role" value="{{ request('role') }}">
                    </div>
                    <div class="filter-col-2">
                        <select name="category" class="form-select">
                            <option value="">Any Category</option>
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-col-2">
                        <select name="status" class="form-select">
                            <option value="">Any Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="filter-col-2">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="filter-col-1 d-grid">
                        <button type="submit" class="btn btn-outline-secondary">Filter</button>
                    </div>
                    <div class="filter-col-2 d-grid">
                        <a href="{{ route('feedback.admin') }}" class="btn btn-pink">Clear</a>
                    </div>
                    </div>
                </form>
            </div>

            <div class="card p-3 shadow-sm rounded-3 table-shell">
                <div class="table-responsive">
                    <table class="table table-hover align-middle bg-white rounded-4 mb-0">
                        <thead class="table-pink">
                            <tr>
                                <th>Topic</th>
                                <th>User</th>
                                <th>Course</th>
                                <th>Role</th>
                                <th>Category</th>
                                <th>Replies</th>
                                <th>Status</th>
                                <th>Date Posted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbacks as $feedback)
                            <tr>
                                <td>
                                    <a href="{{ route('feedback.show', $feedback->id) }}" class="topic-link">
                                        {{ $feedback->title ?: 'Untitled Topic' }}
                                    </a>
                                    <div class="topic-snippet mt-1">
                                        {{ \Illuminate\Support\Str::limit($feedback->message, 90) }}
                                    </div>
                                </td>
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
                                    <span class="category-chip">{{ $categoryOptions[$feedback->category] ?? ucfirst(str_replace('_', ' ', $feedback->category)) }}</span>
                                </td>
                                <td>
                                    {{ $feedback->replies->count() }}
                                </td>
                                <td>
                                    <span class="status-chip status-{{ $feedback->status }}">{{ $feedback->status }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($feedback->created_at)->format('F j, Y g:i A') }}</td>

                                <td class="actions-cell">
                                    <form method="POST" action="{{ route('feedback.status.update', $feedback->id) }}" class="status-editor">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="open" {{ $feedback->status === 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ $feedback->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Save</button>
                                    </form>
                                    <form method="POST" action="{{ route('feedback.delete', $feedback->id) }}" onsubmit="return confirm('Delete this feedback?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-red w-100">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No feedback found.</td>
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