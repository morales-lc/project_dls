<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $thread->title }} - Feedback Forum</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        body {
            background: linear-gradient(180deg, #fff7fb 0%, #ffffff 100%);
        }

        .thread-shell {
            max-width: 960px;
            margin: 0 auto;
            padding: 2rem 0 3rem;
        }

        .thread-card {
            border: 1px solid rgba(216, 27, 96, 0.16);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(216, 27, 96, 0.09);
        }

        .reply-card {
            border: 1px solid rgba(216, 27, 96, 0.1);
            border-radius: 12px;
            background: #fff;
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
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.25rem 0.58rem;
            background: rgba(216, 27, 96, 0.12);
            color: #a91549;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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

        .btn-forum {
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #d81b60 0%, #a91549 100%);
        }

        .btn-forum:hover {
            color: #fff;
            filter: brightness(0.97);
        }
    </style>
</head>

<body>
    @include('navbar')

    <div class="container thread-shell">
        <div class="mb-3">
            <a href="{{ route('feedback.form') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Forum
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <article class="thread-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <h1 class="h3 mb-0">{{ $thread->title }}</h1>
                <span class="status-chip status-{{ $thread->status }}">{{ $thread->status }}</span>
            </div>
            <div class="mb-2">
                <span class="category-chip">{{ \App\Models\Feedback::categoryOptions()[$thread->category] ?? ucfirst(str_replace('_', ' ', $thread->category)) }}</span>
            </div>
            <div class="small text-muted mb-3">
                <i class="bi bi-person-circle me-1"></i>{{ $thread->user ? $thread->user->name : 'Unavailable' }}
                <span class="mx-2">•</span>
                <i class="bi bi-clock me-1"></i>{{ $thread->created_at->format('F j, Y g:i A') }}
            </div>
            <p class="mb-0" style="white-space: pre-line;">{{ $thread->message }}</p>
        </article>

        <section class="mb-4">
            <h2 class="h5 mb-3">Replies ({{ $thread->replies->count() }})</h2>
            <div class="d-grid gap-3">
                @forelse($thread->replies as $reply)
                    <article class="reply-card p-3">
                        <div class="small text-muted mb-2">
                            <i class="bi bi-person-circle me-1"></i>{{ $reply->user ? $reply->user->name : 'Unavailable' }}
                            @if($reply->user && $reply->user->role === 'admin')
                                <span class="admin-chip">Admin</span>
                            @endif
                            <span class="mx-2">•</span>
                            <i class="bi bi-clock me-1"></i>{{ $reply->created_at->diffForHumans() }}
                        </div>
                        <p class="mb-0" style="white-space: pre-line;">{{ $reply->message }}</p>
                    </article>
                @empty
                    <div class="text-muted">No replies yet. Start the discussion.</div>
                @endforelse
            </div>
        </section>

        <section class="thread-card p-4">
            <h3 class="h5 mb-3">Post a Reply</h3>

            @if($thread->status !== 'open')
                <div class="alert alert-warning mb-0">
                    This topic is {{ $thread->status }}. New replies are disabled.
                </div>
            @else
                <form method="POST" action="{{ route('feedback.reply', $thread->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reply</label>
                        <textarea name="message" class="form-control" rows="4" maxlength="2000" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-forum px-4">Post Reply</button>
                </form>
            @endif
        </section>
    </div>

    @include('footer')
</body>

</html>
