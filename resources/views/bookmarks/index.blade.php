<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarked Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body>
    @include('navbar')

    <div class="d-flex" style="min-height: 80vh; background: #f8f9fa;">
        @include('sidebar')

        <div class="flex-grow-1 d-flex justify-content-center align-items-start py-5">
            <div class="card shadow-sm w-100" style="max-width: 1100px;">
                <div class="card-header bg-pink text-white d-flex align-items-center">
                    <i class="bi bi-bookmark-heart-fill fs-3 me-2"></i>
                    <span class="fw-bold fs-5">Bookmarked Items</span>
                </div>
                <div class="card-body">
                    @if($bookmarks->isEmpty())
                        <div class="alert alert-info mb-0">You have no bookmarks yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 110px;">Type</th>
                                        <th>Title / Info</th>
                                        <th style="width: 120px;">Added</th>
                                        <th style="width: 120px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookmarks as $bm)
                                        <tr>
                                            <td class="text-capitalize text-dark">{{ class_basename($bm->bookmarkable_type) }}</td>
                                            <td class="text-dark">
                                                @if($bm->bookmarkable)
                                                    @php $item = $bm->bookmarkable; @endphp
                                                    <div class="fw-semibold fs-6">{{ $item->title ?? ($item->name ?? 'Item') }}</div>
                                                    <div class="small text-muted">{{ $item->author ?? '' }} {{ $item->year ?? '' }}</div>
                                                @else
                                                    <em class="text-danger">Item removed</em>
                                                @endif
                                            </td>
                                            <td class="text-dark small">{{ $bm->created_at->diffForHumans() }}</td>
                                            <td>
                                                @if($bm->bookmarkable)
                                                    @php
                                                        $route = null;
                                                        if($bm->bookmarkable_type === \App\Models\MidesDocument::class) {
                                                            $route = route('mides.viewer', $bm->bookmarkable->id);
                                                        }
                                                    @endphp
                                                    @if($route)
                                                        <a href="{{ $route }}" class="btn btn-sm btn-outline-primary me-1" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open</a>
                                                    @endif
                                                @endif
                                                <form action="{{ route('bookmarks.toggle') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $bm->bookmarkable_id }}">
                                                    <input type="hidden" name="type" value="mides">
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i> Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('footer')
</body>
</html>
