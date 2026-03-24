@extends('layouts.management')

@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Feedback Details')

@section('content')
<style>
    :root {
        --fb-pink: #e83e8c;
        --fb-pink-deep: #c2185b;
        --fb-line: #f0d9e4;
        --fb-muted: #6b7280;
    }

    .feedback-detail-shell {
        max-width: 980px;
        margin: auto;
    }

    .feedback-detail-main {
        background: linear-gradient(180deg, #fff 0%, #fff9fc 100%);
        border: 1px solid var(--fb-line);
    }

    .feedback-detail-title {
        color: var(--fb-pink-deep);
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

    .admin-chip {
        background: rgba(29, 78, 216, 0.12);
        color: #1d4ed8;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.18rem 0.45rem;
        margin-left: 0.4rem;
    }

    .btn-pink {
        background-color: var(--fb-pink);
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-pink:hover {
        background-color: #d63384;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-red {
        background-color: #dc3545;
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-red:hover {
        background-color: #bb2d3b;
        color: #fff;
        transform: translateY(-1px);
    }

    .reply-item {
        border: 1px solid #edd5e2;
        border-radius: 12px;
        background: #fff;
    }
</style>

<div class="py-5">
    <div class="feedback-detail-shell card shadow rounded-4 w-100 feedback-detail-main">
        <div class="card-body p-4 p-lg-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="fw-bold mb-0 feedback-detail-title">Feedback Actions</h2>
                <a href="{{ route('feedback.admin') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Feedback List
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                        <h3 class="h5 mb-0">{{ $feedback->title ?: 'Untitled Topic' }}</h3>
                        <span class="status-chip status-{{ $feedback->status }}">{{ $feedback->status }}</span>
                    </div>

                    <div class="mb-2">
                        <span class="category-chip">{{ $categoryOptions[$feedback->category] ?? ucfirst(str_replace('_', ' ', $feedback->category)) }}</span>
                    </div>

                    <div class="small text-muted mb-3">
                        <i class="bi bi-person-circle me-1"></i>{{ $feedback->user ? $feedback->user->name : 'Unavailable' }}
                        <span class="mx-2">|</span>
                        <i class="bi bi-clock me-1"></i>{{ $feedback->created_at->format('F j, Y g:i A') }}
                    </div>

                    <p class="mb-0" style="white-space: pre-line;">{{ $feedback->message }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body p-3 p-lg-4">
                    <h4 class="h6 mb-3">Admin Actions</h4>

                    <form method="POST" action="{{ route('feedback.status.update', $feedback->id) }}" class="row g-2 align-items-center mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="col-sm-8 col-md-5">
                            <select name="status" class="form-select form-select-sm">
                                <option value="open" {{ $feedback->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $feedback->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-auto">
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Update Status</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('feedback.admin.reply', $feedback->id) }}" class="mb-3">
                        @csrf
                        <label class="form-label fw-semibold">Admin Reply / Update</label>
                        <textarea name="message" class="form-control" rows="4" maxlength="2000" placeholder="Write your response to this feedback..." required></textarea>
                        <button type="submit" class="btn btn-pink mt-2">Send Message</button>
                    </form>

                    <form method="POST" action="{{ route('feedback.delete', $feedback->id) }}" onsubmit="return confirm('Delete this feedback and all replies?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red">Delete Topic</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 p-lg-4">
                    <h4 class="h6 mb-3">Replies ({{ $feedback->replies->count() }})</h4>

                    <div class="d-grid gap-3">
                        @forelse($feedback->replies as $reply)
                            <article class="reply-item p-3">
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-person-circle me-1"></i>{{ $reply->user ? $reply->user->name : 'Unavailable' }}
                                    @if($reply->user && $reply->user->role === 'admin')
                                        <span class="admin-chip">Admin</span>
                                    @endif
                                    <span class="mx-2">|</span>
                                    <i class="bi bi-clock me-1"></i>{{ $reply->created_at->diffForHumans() }}
                                </div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $reply->message }}</p>
                            </article>
                        @empty
                            <div class="text-muted">No replies yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
