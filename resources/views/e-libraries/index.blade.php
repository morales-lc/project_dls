<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Libraries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Responsive fixes */
        @media (max-width: 768px) {
            .elib-card {
                flex-direction: column;
                align-items: flex-start;
                text-align: center;
            }

            .elib-logo {
                max-width: 100%;
                max-height: 140px;
                margin: 0 auto 1rem auto;
                display: block;
            }

            .elib-title {
                font-size: 1.1rem;
            }

            .elib-desc {
                font-size: 0.9rem;
            }
        }

        /* Even smaller screens */
        @media (max-width: 480px) {
            .hero {
                padding: 1.5rem 1rem;
            }

            .elib-card {
                padding: 1rem;
            }

            .elib-title {
                font-size: 1rem;
            }

            .elib-desc {
                font-size: 0.85rem;
            }
        }

        :root {
            --brand-pink: #d81b60;
            --soft-gray: #f5f6fa;
            --soft-blue: #f0f3f9;
        }

        body {
            background: var(--soft-gray);
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #ffe6ef, #f3f7ff);
            border-radius: 18px;
            padding: 2.25rem 1.5rem;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        }

        .hero h1 {
            color: var(--brand-pink);
            font-weight: 800;
            letter-spacing: .5px;
        }

        .hero p {
            color: #555;
            margin-bottom: 1rem;
        }

        .search-input {
            border-radius: 12px;
            padding: .75rem 1rem;
        }

        /* Cards */
        .elib-card {
            transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
            cursor: pointer;
            min-height: 300px;
            display: flex;
            align-items: center;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .elib-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
            background: #fff5f7;
        }

        .elib-card:nth-child(even) {
            background: var(--soft-blue);
        }

        .elib-logo {
            max-width: 200px;
            max-height: 180px;
            object-fit: contain;
            margin-right: 1.25rem;
        }

        .elib-title {
            font-family: "Georgia", serif;
            font-weight: 700;
            color: #b23a48;
            font-size: 1.25rem;
        }

        .elib-desc {
            color: #555;
            line-height: 1.55;
        }

        .elib-badges .badge {
            font-weight: 500;
        }

        /* Modal enhancements */
        .elib-modal-header {
            background: linear-gradient(135deg, #ffe6ef, #f3f7ff);
        }

        .elib-modal-header .modal-title {
            color: var(--brand-pink);
            font-weight: 800;
        }

        .elib-modal-body {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 1rem;
        }

        .elib-modal-logo {
            width: 100%;
            max-height: 160px;
            object-fit: contain;
            border-radius: .75rem;
            background: #fff;
            padding: .75rem;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .elib-instructions {
            max-height: 340px;
            overflow: auto;
            border-radius: .5rem;
            background: #fff;
            padding: 1rem;
            border: 1px solid #eee;
        }

        @media (max-width: 767px) {
            .elib-modal-body {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    @include('navbar')
    <div class="container py-5">
        <div class="hero mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="flex-grow-1">
                    <h1 class="mb-1">Online Databases</h1>
                    <p class="mb-0">Explore licensed databases available through the Learning Commons.</p>
                </div>
            </div>
        </div>

        @if($libraries->isEmpty())
        <div class="alert alert-info text-center">No e-libraries available right now.</div>
        @else
        <div id="elibList" class="vstack gap-4">
            @foreach($libraries as $lib)
            <div class="elib-card"
                data-bs-toggle="modal"
                data-bs-target="#libModal{{ $lib->id }}"
                data-name="{{ Str::lower($lib->name) }}"
                data-desc="{{ Str::lower($lib->description) }}">

                @if($lib->image)
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/'.$lib->image) }}"
                        alt="{{ $lib->name }} Logo"
                        class="elib-logo">
                </div>
                @endif

                <div>
                    <h5 class="elib-title mb-2">{{ $lib->name }}</h5>
                    <div class="elib-badges mb-2">
                        @if($lib->username || $lib->password)
                        <span class="badge text-bg-warning">Credentials required</span>
                        @else
                        <span class="badge text-bg-success">Open access</span>
                        @endif
                    </div>
                    <p class="elib-desc mb-0">{{ $lib->description }}</p>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="libModal{{ $lib->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header elib-modal-header">
                            <h5 class="modal-title">{{ $lib->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="elib-modal-body">
                                <div>
                                    @if($lib->image)
                                    <img src="{{ asset('storage/'.$lib->image) }}" alt="{{ $lib->name }} Logo" class="elib-modal-logo">
                                    @else
                                    <div class="elib-modal-logo d-flex align-items-center justify-content-center" style="background:#fafafa;">
                                        <span class="text-muted">No image</span>
                                    </div>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ $lib->link }}" target="_blank" class="btn btn-success w-100">Open Database</a>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-2">{{ $lib->description }}</p>
                                    <div class="elib-instructions mb-3">
                                        @if($lib->instructions)
                                        {!! $lib->instructions !!}
                                        @else
                                        <p class="text-muted mb-0">No additional instructions provided.</p>
                                        @endif
                                    </div>

                                    @if($lib->username || $lib->password)
                                    <div class="bg-light p-3 rounded border">
                                        @if($lib->username)
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-2">Username:</strong>
                                            <code id="user{{ $lib->id }}">{{ $lib->username }}</code>
                                            <button class="btn btn-sm btn-outline-primary ms-2 copy-btn" data-copy="user{{ $lib->id }}">Copy</button>
                                        </div>
                                        @endif
                                        @if($lib->password)
                                        <div class="d-flex align-items-center">
                                            <strong class="me-2">Password:</strong>
                                            <code id="pass{{ $lib->id }}">{{ $lib->password }}</code>
                                            <button class="btn btn-sm btn-outline-primary ms-2 copy-btn" data-copy="pass{{ $lib->id }}">Copy</button>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @include('footer')

    <script>
        // Copy buttons feedback
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.copy-btn');
            if (!btn) return;
            const id = btn.getAttribute('data-copy');
            const el = id ? document.getElementById(id) : null;
            if (!el) return;
            const text = el.textContent || el.innerText || '';
            navigator.clipboard.writeText(text).then(() => {
                const prev = btn.textContent;
                btn.textContent = 'Copied!';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.textContent = prev;
                    btn.classList.add('btn-outline-primary');
                    btn.classList.remove('btn-success');
                }, 1200);
            });
        });

        // Client-side filter
        const searchInput = document.getElementById('elibSearch');
        const list = document.getElementById('elibList');
        if (searchInput && list) {
            searchInput.addEventListener('input', function() {
                const q = (this.value || '').toLowerCase().trim();
                list.querySelectorAll('.elib-card').forEach(card => {
                    const name = (card.getAttribute('data-name') || '');
                    const desc = (card.getAttribute('data-desc') || '');
                    const show = !q || name.includes(q) || desc.includes(q);
                    card.style.display = show ? '' : 'none';
                });
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>