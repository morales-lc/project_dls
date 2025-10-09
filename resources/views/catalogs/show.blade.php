@include('navbar')

<style>
/* General container spacing */
.catalog-detail-wrap { padding: 2.25rem 0; }

/* Book cover styling */
.catalog-cover {
    width:100%;
    height:320px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#fff;
    border-radius:6px;
    overflow:hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.catalog-cover img { 
    max-width:100%; 
    max-height:100%; 
    object-fit:contain; 
}

/* Meta info */
.catalog-meta .label {
    font-size: .9rem;
    color: #6c757d;
}
.catalog-meta .value {
    font-weight:600;
    color:#222;
    word-break: break-word;
}

/* Buttons */
.btn-pink {
    background-color:#e83e8c;
    color:#fff;
    border:none;
}
.btn-pink:hover {
    background-color:#d63384;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    h3 { font-size: 1.25rem; }
    .catalog-meta .label { font-size: .85rem; }
    .catalog-meta .value { font-size: .95rem; }
    .catalog-cover { height:240px; }
}

@media (max-width: 767px) {
    .catalog-detail-wrap { padding: 1.25rem 0; }
    .catalog-cover { height:200px; }
    .catalog-meta .col { flex: 1 1 100%; }
    .row-cols-md-2 > * { flex: 1 1 100%; }
    .mt-4.d-flex { flex-direction: column; align-items: stretch; }
    .mt-4.d-flex a { width: 100%; }
    .carousel-control-prev-icon, .carousel-control-next-icon {
        width: 1.5rem;
        height: 1.5rem;
    }
    .rec-card .card-body { flex-direction: row; }
}
</style>

<div style="height: 60px;"></div>

<div class="container catalog-detail-wrap">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="bg-white rounded shadow-sm p-3" style="border:1px solid #ffdfe9;">
                        <div class="catalog-cover">
                            <img src="{{ asset('images/book-placeholder.png') }}" alt="Cover">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8">
                    <h3 class="fw-bold mb-2">{{ $catalog->title }}</h3>
                    @if(!empty($catalog->author))
                        <div class="text-muted mb-2">{{ $catalog->author }}</div>
                    @endif

                    <div class="row row-cols-1 row-cols-md-2 g-2 catalog-meta">
                        @if(!empty($catalog->call_number))
                        <div class="col">
                            <div class="label">Call Number</div>
                            <div class="value">{{ $catalog->call_number }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->sublocation))
                        <div class="col">
                            <div class="label">Sublocation</div>
                            <div class="value">{{ $catalog->sublocation }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->publisher))
                        <div class="col">
                            <div class="label">Publisher</div>
                            <div class="value">{{ $catalog->publisher }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->year))
                        <div class="col">
                            <div class="label">Year</div>
                            <div class="value">{{ $catalog->year }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->edition))
                        <div class="col">
                            <div class="label">Edition</div>
                            <div class="value">{{ $catalog->edition }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->isbn))
                        <div class="col">
                            <div class="label">ISBN</div>
                            <div class="value">{{ $catalog->isbn }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->issn))
                        <div class="col">
                            <div class="label">ISSN</div>
                            <div class="value">{{ $catalog->issn }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->lccn))
                        <div class="col">
                            <div class="label">LCCN</div>
                            <div class="value">{{ $catalog->lccn }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->barcode))
                        <div class="col">
                            <div class="label">Barcode</div>
                            <div class="value">{{ $catalog->barcode }}</div>
                        </div>
                        @endif

                        @if(!empty($catalog->series))
                        <div class="col">
                            <div class="label">Series</div>
                            <div class="value">{{ $catalog->series }}</div>
                        </div>
                        @endif
                    </div>

                    @if(!empty($catalog->additional_info))
                        <div class="mt-4">
                            <h6 class="fw-semibold">Additional Info</h6>
                            <div class="small">{{ $catalog->additional_info }}</div>
                        </div>
                    @endif

                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-primary" href="#">Add to list</a>
                        <a class="btn btn-pink" href="#">Request</a>
                    </div>

                    @if(isset($recommendations) && $recommendations->count())
                        <div class="mt-5">
                            <h5 class="fw-bold">Recommended for you</h5>
                            <style>
                                .rec-card .card-body { padding: .6rem .8rem; }
                                .rec-cover {
                                    width:44px; height:60px;
                                    display:flex; align-items:center; justify-content:center;
                                    background:#f8f9fa;
                                    border-radius:4px;
                                }
                            </style>

                            <div id="recCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-wrap="true">
                                <div class="carousel-inner">
                                    @php $chunks = $recommendations->chunk(3); @endphp
                                    @foreach($chunks as $i => $chunk)
                                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                                            <div class="row g-3">
                                                @foreach($chunk as $rec)
                                                    <div class="col-12 col-md-4">
                                                        <div class="card h-100 rec-card shadow-sm">
                                                            <div class="card-body d-flex align-items-start">
                                                                <div class="rec-cover me-3">
                                                                    <img src="{{ asset('images/book-placeholder.png') }}" alt="cover" style="max-width:100%; max-height:100%; object-fit:contain;" />
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold">{{ Str::limit($rec->title, 60) }}</div>
                                                                    @if($rec->author)
                                                                        <div class="small text-muted">{{ Str::limit($rec->author, 36) }}</div>
                                                                    @endif
                                                                </div>
                                                                <a href="{{ route('catalogs.show', $rec->id) }}" class="stretched-link"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button class="carousel-control-prev" type="button" data-bs-target="#recCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#recCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height: 200px;"></div>

@include('footer')
