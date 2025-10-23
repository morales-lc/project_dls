@extends('layouts.management')
@push('management-head')
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
@endpush
@section('title','Post Management')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
    <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1100px; background: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
            <h2 class="fw-bold mb-0" style="letter-spacing: 1px; color: #d81b60; font-size: 2rem;">Manage Posts & Announcements</h2>

        </div>
        <div class="mb-3"></div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Add New Post / Announcement</h4>

        </div>
    <a href="{{ route('post.create', ['return' => request()->fullUrl()]) }}" class="btn btn-pink px-4 py-2" style="font-weight:600; font-size:1.05rem;"><i class="bi bi-plus-lg"></i> Add New Post</a>
        <div style="height: 12px;"></div>
    @php
    $types = ['Announcement', 'Event', 'Update', 'Post'];
    $requestedTab = request('tab');
    $pageKeys = array_map(function($t){ return strtolower($t) . '_page'; }, $types);
    $anyPage = false;
    foreach ($pageKeys as $pk) { if (request()->has($pk)) { $anyPage = true; break; } }
    @endphp
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="postTypeTabs" role="tablist">
            @foreach($types as $i => $type)
            <li class="nav-item" role="presentation">
                @php
                    $tabKey = strtolower($type);
                    $isActive = ($requestedTab === $tabKey) || (!$requestedTab && $anyPage && request()->has($tabKey . '_page')) || (!$requestedTab && !$anyPage && $i === 0);
                @endphp
                <button class="nav-link {{ $isActive ? 'active' : '' }}" id="tab-{{ $tabKey }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $tabKey }}" type="button" role="tab" aria-controls="pane-{{ $tabKey }}" aria-selected="{{ $isActive ? 'true' : 'false' }}">
                    {{ $type == 'Post' ? 'Latest Posts' : $type . 's' }}
                </button>
            </li>
            @endforeach
        </ul>
        <div class="card p-3 shadow rounded-4 w-100" style="max-width:1100px;">
            <div class="tab-content" id="postTypeTabsContent">
                @foreach($types as $i => $type)
                @php
                $posts = $paginatedPosts[$type];
                @endphp
                @php
                    $tabKey = strtolower($type);
                    $isActive = ($requestedTab === $tabKey) || (!$requestedTab && $anyPage && request()->has($tabKey . '_page')) || (!$requestedTab && !$anyPage && $i === 0);
                @endphp
                <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                    id="pane-{{ $tabKey }}" role="tabpanel"
                    aria-labelledby="tab-{{ $tabKey }}">
                    <div class="card shadow rounded-4 border-0 mb-5">
                        <div class="card-header bg-white border-bottom-0">
                            <h5 class="mb-0">{{ $type == 'Post' ? 'Latest Posts' : $type . 's' }}</h5>
                        </div>
                        <div class="card-body">
                            @if($posts->count())
                            <div class="list-group">
                                @foreach($posts as $post)
                                @php
                                $thumbUrl = null;
                                $ytid = null;
                                if ($post->photo) {
                                $thumbUrl = asset('storage/' . $post->photo);
                                }
                                if (!$thumbUrl && $post->youtube_link) {
                                preg_match('/v=([^&]+)/', $post->youtube_link, $m);
                                $ytid = $m[1] ?? null;
                                if (!$ytid) {
                                $thumbUrl = 'https://img.youtube.com/vi/' . ($m[1] ?? 'unknown') . '/hqdefault.jpg';
                                }
                                }
                                if (!$thumbUrl && !$ytid && $post->website_link) {
                                $ogThumb = $post->og_image ?? null;
                                $base = parse_url($post->website_link, PHP_URL_SCHEME) . '://' . parse_url($post->website_link, PHP_URL_HOST);
                                $thumbUrl = $ogThumb ?: ($base . '/favicon.ico');
                                }
                                @endphp

                                <div class="list-group-item mb-3 p-3"
                                    style="min-height:220px;display:flex;align-items:stretch;">
                                    <div class="row g-3 w-100 align-items-start">
                                        <!-- Media -->
                                        <div class="col-md-4 d-flex flex-column align-items-center">
                                            <span class="badge bg-secondary mb-2 align-self-start">{{ $post->type }}</span>
                                            <div class="mt-1 w-100">
                                                @if($ytid)
                                                <div class="rounded overflow-hidden w-100" style="max-width:380px;">
                                                    <iframe src="https://www.youtube.com/embed/{{ $ytid }}"
                                                        title="YouTube video" allowfullscreen
                                                        style="width:100%;height:220px;border:0;border-radius:8px;"></iframe>
                                                </div>
                                                @elseif($thumbUrl)
                                                <img src="{{ $thumbUrl }}" alt="Thumbnail" class="img-fluid rounded w-100"
                                                    style="height:220px;object-fit:cover;max-width:380px;">
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Content -->
                                        <div class="col-md-8 d-flex flex-column justify-content-between"
                                            style="min-height:220px;">
                                            <div class="d-flex flex-column justify-content-start h-100 mt-md-4">
                                                <h5 class="mb-2">{{ $post->title }}</h5>
                                                <p class="text-muted mb-0">{{ $post->description }}</p>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end align-items-end mt-2">
                                                <a href="{{ route('post.edit', [$post->id, 'return' => request()->fullUrl() . (request()->getQueryString() ? '&' : '?') . 'tab=' . strtolower($post->type)]) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form method="POST" action="{{ route('post.delete', $post->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() . (request()->getQueryString() ? '&' : '?') . 'tab=' . strtolower($post->type) }}">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete this post?')">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $posts->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>

                            @else
                            <div class="text-center text-muted py-3">
                                No {{ $type == 'Post' ? 'posts' : strtolower($type) . 's' }} yet.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection