<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Feedback Forum</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        :root {
            --forum-rose: #d81b60;
            --forum-rose-deep: #a91549;
            --forum-ink: #25222b;
            --forum-bg: #fff6fa;
            --forum-card: #ffffff;
        }

        body {
            background:
                radial-gradient(circle at 10% -10%, rgba(216, 27, 96, 0.15), transparent 38%),
                radial-gradient(circle at 90% 10%, rgba(233, 30, 99, 0.1), transparent 32%),
                var(--forum-bg);
        }

        .forum-wrap {
            padding: 2rem 0 3rem;
        }

        .forum-card {
            background: var(--forum-card);
            border: 1px solid rgba(216, 27, 96, 0.12);
            border-radius: 18px;
            box-shadow: 0 12px 40px rgba(60, 26, 43, 0.08);
        }

        .forum-title {
            color: var(--forum-ink);
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .forum-kicker {
            color: var(--forum-rose);
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
        }

        .btn-forum {
            border: none;
            color: #fff;
            background: linear-gradient(135deg, var(--forum-rose) 0%, var(--forum-rose-deep) 100%);
        }

        .btn-forum:hover {
            color: #fff;
            filter: brightness(0.97);
        }

        .thread-item {
            border: 1px solid rgba(216, 27, 96, 0.14);
            border-radius: 14px;
            padding: 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            background: #fff;
        }

        .thread-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(216, 27, 96, 0.13);
        }

        .thread-title {
            color: #2f2230;
            font-weight: 700;
            text-decoration: none;
        }

        .thread-title:hover {
            color: var(--forum-rose);
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

        .topic-mobile-toggle {
            border: 1px solid rgba(216, 27, 96, 0.28);
            color: #9a1144;
            background: #fff;
            font-weight: 600;
            border-radius: 10px;
            display: none;
            width: 100%;
        }

        .topic-mobile-toggle:hover {
            background: rgba(216, 27, 96, 0.08);
            color: #9a1144;
        }

        @media (max-width: 991.98px) {
            .topic-mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.45rem;
                margin-bottom: 0.85rem;
            }

            .topic-composer.is-collapsed {
                display: none;
            }
        }
    </style>
</head>

<body>
    @include('navbar')
    <div class="container forum-wrap">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="forum-card p-4 sticky-top" style="top: 90px;">
                    <div class="forum-kicker mb-2">Community Board</div>
                    <h2 class="forum-title h3 mb-3">Start a New Topic</h2>
                    <button type="button" id="topicComposerToggle" class="btn topic-mobile-toggle">
                        <i class="bi bi-chevron-up" id="topicComposerToggleIcon"></i>
                        <span id="topicComposerToggleText">Minimize topic form</span>
                    </button>

                    <div id="topicComposerBody" class="topic-composer">
                    <p class="text-muted small mb-4">Share ideas, report issues, or suggest service improvements. Threads can be replied to by other users.</p>

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

                    <form method="POST" action="{{ route('feedback.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Topic Title <span class="text-danger">*</span></label>
                            <input name="title" class="form-control" maxlength="120" required value="{{ old('title') }}" placeholder="Ex: Library hours on weekends">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                @foreach($categoryOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('category', 'general') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_anonymous" id="isAnonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isAnonymous">Post anonymously</label>
                        </div>

                        <button type="submit" class="btn btn-forum w-100 py-2 fw-semibold">Post Topic</button>
                    </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="forum-card p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h3 class="h4 mb-0 forum-title">Recent Topics</h3>
                        <form method="GET" action="{{ route('feedback.form') }}" class="d-flex gap-2 flex-wrap justify-content-end">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search topics">
                            <select name="category" class="form-select form-select-sm" style="min-width: 180px;">
                                <option value="">All categories</option>
                                @foreach($categoryOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-secondary btn-sm" type="submit">Search</button>
                        </form>
                    </div>

                    <div class="d-grid gap-3">
                        @forelse($threads as $thread)
                            <article class="thread-item">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <a class="thread-title" href="{{ route('feedback.show', $thread->id) }}">
                                        {{ $thread->title ?: 'Untitled Topic' }}
                                    </a>
                                    <span class="status-chip status-{{ $thread->status }}">{{ $thread->status }}</span>
                                </div>

                                <div class="mb-2">
                                    <span class="category-chip">{{ $categoryOptions[$thread->category] ?? ucfirst(str_replace('_', ' ', $thread->category)) }}</span>
                                </div>

                                <p class="text-muted mb-2" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($thread->message, 180) }}</p>

                                <div class="d-flex flex-wrap gap-3 small text-muted">
                                    <span><i class="bi bi-person-circle me-1"></i>{{ $thread->user ? $thread->user->name : 'Anonymous' }}</span>
                                    <span><i class="bi bi-chat-square-dots me-1"></i>{{ $thread->replies->count() }} replies</span>
                                    <span><i class="bi bi-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}</span>
                                </div>
                            </article>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-chat-left-text" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No topics yet. Be the first to start one.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $threads->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('topicComposerToggle');
            var body = document.getElementById('topicComposerBody');
            var icon = document.getElementById('topicComposerToggleIcon');
            var text = document.getElementById('topicComposerToggleText');

            if (!toggleBtn || !body || !icon || !text) {
                return;
            }

            function isMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function setExpanded(expanded) {
                if (!isMobile()) {
                    body.classList.remove('is-collapsed');
                    icon.className = 'bi bi-chevron-up';
                    text.textContent = 'Minimize topic form';
                    return;
                }

                body.classList.toggle('is-collapsed', !expanded);
                icon.className = expanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
                text.textContent = expanded ? 'Minimize topic form' : 'Show topic form';
            }

            var expanded = true;
            setExpanded(expanded);

            toggleBtn.addEventListener('click', function () {
                expanded = !expanded;
                setExpanded(expanded);
            });

            window.addEventListener('resize', function () {
                setExpanded(expanded);
            });
        });
    </script>
</body>

</html>