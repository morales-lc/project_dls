<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LiRA History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

  <style>
    .bg-pink {
      background-color: #ffd1e3 !important;
      color: #d81b60 !important;
    }

    .text-pink {
      color: #d81b60 !important;
    }

    .btn-outline-pink {
      border: 1.5px solid #ffd1e3 !important;
      color: #d81b60 !important;
      background-color: #fff !important;
      font-weight: 500;
      border-radius: 0.7rem;
      transition: 0.2s;
    }

    .btn-outline-pink:hover {
      background-color: #ffd1e3 !important;
      color: #b3134b !important;
    }

    .badge.bg-pink {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 600;
      border: 1px solid #ffd1e3 !important;
    }

    .card-header.bg-pink {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 700;
      border-bottom: 2px solid #ffd1e3;
    }

    thead tr {
      background: #ffd1e3 !important;
      color: #d81b60 !important;
    }

    .card-body {
      background: linear-gradient(180deg, #fff 90%, #ffe3ef 100%);
    }

    .card {
      border-radius: 1.5rem;
      overflow: hidden;
      background: #ffffff;
      border: 1px solid #ffd1e3;
    }

    .table {
      border-collapse: separate;
      border-spacing: 0 0.6rem;
    }

    .history-table {
      min-width: 1180px;
    }

    .history-table thead th {
      background: linear-gradient(180deg, #fff8fc 0%, #fff1f7 100%) !important;
      color: #8f2f5c !important;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-size: 0.78rem;
      font-weight: 700;
      border-bottom: 1px solid #f7d8e7 !important;
      padding: 1rem 1rem !important;
    }

    .table tbody tr {
      background: #fff;
      border-radius: 0.75rem;
      box-shadow: 0 10px 24px rgba(216, 27, 96, 0.08);
      transition: all 0.25s ease;
    }

    .table tbody tr:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 30px rgba(216, 27, 96, 0.14);
      background-color: #fff8fb !important;
    }

    .table td,
    .table th {
      border: none !important;
      vertical-align: middle;
      padding: 1.1rem 1rem !important;
    }

    .table td .fw-semibold {
      font-size: 1rem;
      color: #c2185b !important;
    }

    .table td .small.text-muted {
      font-size: 0.875rem;
      color: #7a7a7a !important;
    }

    .nav-tabs .nav-link {
      border: none;
      color: #c2185b;
      font-weight: 500;
      transition: 0.2s;
    }

    .nav-tabs .nav-link.active {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 700;
      border-radius: 0.75rem 0.75rem 0 0;
      box-shadow: inset 0 -3px 0 #d81b60;
    }

    .alert-info {
      background-color: #fff4f8 !important;
      color: #d81b60 !important;
      border: 1px solid #ffd1e3 !important;
      font-weight: 500;
    }

    .history-stat {
      border: 1px solid #ffd7e8;
      background: linear-gradient(180deg, #fff, #fff7fb);
      border-radius: 1rem;
      padding: 1rem 1.1rem;
      box-shadow: 0 6px 20px rgba(216, 27, 96, 0.08);
      height: 100%;
    }

    .history-stat-label {
      color: #9b5a75;
      font-size: 0.82rem;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .history-stat-value {
      color: #d81b60;
      font-size: 1.65rem;
      font-weight: 700;
      line-height: 1.2;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.38rem 0.72rem;
      border-radius: 999px;
      font-size: 0.82rem;
      font-weight: 600;
      border: 1px solid transparent;
      white-space: nowrap;
    }

    .status-badge.pending {
      background: #f3f4f6;
      color: #6b7280;
      border-color: #e5e7eb;
    }

    .status-badge.accepted {
      background: #fff3cd;
      color: #946200;
      border-color: #ffe69c;
    }

    .status-badge.current {
      background: #e7f8ef;
      color: #1b7f46;
      border-color: #bfe8cf;
    }

    .status-badge.completed {
      background: #f5ebff;
      color: #7b2cbf;
      border-color: #dfc7ff;
    }

    .status-badge.returned {
      background: #e7f1ff;
      color: #1e5db5;
      border-color: #bed6ff;
    }

    .status-badge.not-approved {
      background: #ffe8ea;
      color: #bb2d3b;
      border-color: #f5c2c7;
    }

    .history-meta-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.35rem 0.65rem;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 600;
      border: 1px solid #ffd1e3;
      background: #fff6fa;
      color: #c2185b;
    }

    .history-note {
      background: linear-gradient(180deg, #fff9fc 0%, #fff4f8 100%);
      border: 1px solid #f7d6e5;
      border-radius: 1rem;
      padding: 0.9rem 1rem;
      color: #9f275f;
      min-width: 220px;
      line-height: 1.6;
    }

    .history-note-label {
      display: block;
      margin-bottom: 0.4rem;
      font-size: 0.72rem;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #a75d82;
      font-weight: 700;
    }

    .history-note-line + .history-note-line {
      margin-top: 0.35rem;
    }

    .history-request-title {
      font-size: 1.08rem;
      line-height: 1.55;
      color: #c92f72;
      max-width: 420px;
    }

    .history-request-extra {
      margin-top: 0.85rem;
      color: #7a7178 !important;
      line-height: 1.55;
      max-width: 430px;
    }

    .history-request-extra .small {
      display: block;
      color: #7a7178 !important;
    }

    .history-date-block {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      gap: 0.25rem;
      min-width: 126px;
      line-height: 1.35;
    }

    .history-type-pill {
      min-width: 92px;
      justify-content: center;
    }

    .history-action-cell {
      min-width: 120px;
    }

    .history-action-cell .btn-outline-pink {
      min-width: 106px;
      padding: 0.55rem 0.95rem;
      box-shadow: 0 8px 18px rgba(216, 27, 96, 0.08);
    }

    .history-date-card {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-width: 130px;
      padding: 0.9rem 0.95rem;
      border-radius: 1rem;
      border: 1px solid #f6d3e3;
      background: linear-gradient(180deg, #fff9fc 0%, #fff4f8 100%);
      color: #a13367;
      line-height: 1.45;
    }

    .history-date-card-label {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      font-weight: 700;
      color: #b45d85;
      margin-bottom: 0.35rem;
    }

    .history-date-card-value {
      font-size: 0.95rem;
      font-weight: 700;
      color: #c92f72;
    }

    .history-date-card-sub {
      font-size: 0.82rem;
      color: #8f6a7f;
      margin-top: 0.15rem;
    }

    .history-inline-link {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      margin-top: 0.75rem;
      color: #d81b60;
      font-size: 0.88rem;
      font-weight: 600;
      text-decoration: none;
    }

    .history-inline-link:hover {
      color: #b3134b;
      text-decoration: underline;
    }

    .table-responsive-mobile {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 767.98px) {
      .card {
        margin: 0.4rem;
        border-radius: 1rem;
      }

      .nav-tabs {
        flex-wrap: nowrap;
        gap: 0.5rem;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 0.15rem 0.1rem 0.35rem;
        margin-inline: -0.15rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
      }

      .nav-tabs::-webkit-scrollbar {
        display: none;
      }

      .nav-tabs .nav-item {
        flex: 0 0 auto;
      }

      .nav-tabs .nav-link {
        white-space: nowrap;
        padding: 0.72rem 1rem;
        border-radius: 999px;
        border: 1px solid #f6d3e3;
        background: #fff8fb;
        font-size: 0.9rem;
      }

      .nav-tabs .nav-link.active {
        border-radius: 999px;
        box-shadow: 0 10px 20px rgba(216, 27, 96, 0.14);
      }

      thead {
        display: none;
      }

      .table-responsive-mobile {
        overflow-x: visible;
      }

      .history-table {
        min-width: 100%;
      }

      .history-table tbody {
        display: block;
      }

      tr {
        display: block;
        margin-bottom: 0.9rem;
        padding: 0.25rem 0.35rem;
        border: 1px solid #ffe3ef;
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #fff8fb 100%);
      }

      tr td {
        display: grid;
        grid-template-columns: minmax(92px, 110px) 1fr;
        gap: 0.75rem;
        width: 100%;
        padding: 0.8rem 0.85rem !important;
        align-items: start;
        border-top: 1px dashed #f5dce8 !important;
      }

      tr td:first-child {
        border-top: none !important;
        padding-top: 0.55rem !important;
      }

      tr td::before {
        content: attr(data-label);
        display: block;
        font-size: 0.72rem;
        line-height: 1.35;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: #a75d82;
      }

      .card-body {
        padding: 1rem;
      }

      .btn-outline-pink {
        width: 100%;
        margin-top: 0.3rem;
      }

      .table tbody tr {
        box-shadow: 0 8px 22px rgba(216, 27, 96, 0.08);
      }

      .table tbody tr:hover {
        transform: none;
      }

      .history-request-title {
        max-width: none;
        font-size: 1rem;
        line-height: 1.45;
      }

      .history-request-extra {
        margin-top: 0.65rem;
        max-width: none;
      }

      .history-date-block {
        align-items: flex-start;
        min-width: 0;
      }

      .history-note,
      .history-date-card {
        min-width: 0;
        width: 100%;
        align-items: flex-start;
      }

      .history-date-card {
        padding: 0.85rem 0.9rem;
      }

      .history-date-card-sub {
        text-align: left;
      }

      .status-badge {
        justify-content: center;
      }

      .history-inline-link {
        width: 100%;
        justify-content: center;
        padding: 0.78rem 0.9rem;
        border: 1px solid #f6d3e3;
        border-radius: 0.9rem;
        background: #fff;
        margin-top: 0.95rem;
      }

      .history-inline-link:hover {
        text-decoration: none;
        background: #fff6fa;
      }
    }
  </style>
</head>

<body>
  @include('navbar')

  <div class="d-flex" style="min-height: 80vh; background: #f8f9fa;">
    @include('sidebar')

    <div class="flex-grow-1 d-flex justify-content-center align-items-start py-4">
      <div class="w-100" style="max-width: 1100px;">


        <div class="card shadow-lg w-100 border-0">
          <div class="card-header bg-pink d-flex align-items-center justify-content-between" style="border-radius: 1.5rem 1.5rem 0 0;">
            <div class="d-flex align-items-center">
              <i class="bi bi-journal-check fs-3 me-2"></i>
              <span class="fw-bold fs-5">LiRA Borrow History</span>
            </div>
          </div>

          <div class="card-body">
            @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <ul class="nav nav-tabs mb-3" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('lira.history.index') }}">All <span class="badge bg-pink ms-1">{{ $summary['all'] }}</span></a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'currently-borrowing' ? 'active' : '' }}" href="{{ route('lira.history.index', ['tab' => 'currently-borrowing']) }}">Currently Borrowing <span class="badge bg-pink ms-1">{{ $summary['currently_borrowing'] }}</span></a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'returned' ? 'active' : '' }}" href="{{ route('lira.history.index', ['tab' => 'returned']) }}">Returned <span class="badge bg-pink ms-1">{{ $summary['returned'] }}</span></a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'scanning-completed' ? 'active' : '' }}" href="{{ route('lira.history.index', ['tab' => 'scanning-completed']) }}">Scanning Completed <span class="badge bg-pink ms-1">{{ $summary['scanning_completed'] }}</span></a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'not-approved' ? 'active' : '' }}" href="{{ route('lira.history.index', ['tab' => 'not-approved']) }}">Not Approved <span class="badge bg-pink ms-1">{{ $summary['not_approved'] }}</span></a>
              </li>
            </ul>

            @if($items->isEmpty())
            <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #ffe3ef; color: #d81b60; border: 1.5px solid #ffd1e3;">
              <i class="bi bi-journal-x fs-2 me-2"></i>
              @if($tab === 'currently-borrowing')
              No items are currently being borrowed.
              @elseif($tab === 'returned')
              No returned LiRA items yet.
              @elseif($tab === 'scanning-completed')
              No completed LiRA scanning requests yet.
              @elseif($tab === 'not-approved')
              No LiRA requests have been marked as not approved.
              @else
              No LiRA borrow or scanning history yet.
              @endif
            </div>
            @else
            <div class="table-responsive table-responsive-mobile">
              <table class="table history-table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                <thead class="table-light">
                  <tr>
                    <th class="fw-bold" style="width: 42%;">Request</th>
                    <th class="fw-bold text-center" style="width: 16%;">Status</th>
                    <th class="fw-bold text-center" style="width: 14%;">Submitted</th>
                    <th class="fw-bold" style="width: 18%;">Updates</th>
                    <th class="fw-bold text-center" style="width: 10%;">Return / Returned</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($items as $item)
                  @php
                  $requestSummary = trim(preg_replace('/\s+/', ' ', strip_tags($item->for_borrow_scan ?: $item->titles_of ?: ($item->catalog?->title ?? 'Requested item'))));
                  $requestSummary = $requestSummary !== '' ? $requestSummary : 'Requested item';
                  $actionLabel = $item->action === 'scanning' ? 'Scanning' : 'Borrowing';
                  $sourceLabel = $item->catalog ? 'Catalog' : 'LiRA Item';

                  if ($item->status === 'canceled') {
                    $statusClass = 'not-approved';
                    $statusIcon = 'bi-slash-circle-fill';
                    $statusText = 'Canceled';
                  } elseif ($item->status === 'rejected') {
                    $statusClass = 'not-approved';
                    $statusIcon = 'bi-x-circle-fill';
                    $statusText = 'Not Approved';
                  } elseif ($item->loan_status === 'returned') {
                    $statusClass = 'returned';
                    $statusIcon = 'bi-arrow-counterclockwise';
                    $statusText = 'Returned';
                  } elseif ($item->loan_status === 'borrowed') {
                    $statusClass = 'current';
                    $statusIcon = 'bi-check-circle-fill';
                    $statusText = 'Currently Borrowing';
                  } elseif ($item->isSuccessfulFulfillment()) {
                    $statusClass = 'completed';
                    $statusIcon = 'bi-check-circle-fill';
                    $statusText = $item->action === 'scanning' ? 'Scanning Completed' : 'Completed';
                  } elseif ($item->status === 'accepted') {
                    $statusClass = 'accepted';
                    $statusIcon = 'bi-hourglass-split';
                    $statusText = 'Approved';
                  } else {
                    $statusClass = 'pending';
                    $statusIcon = 'bi-clock-history';
                    $statusText = 'Under Review';
                  }
                  @endphp
                  <tr style="background: #fff;">
                    <td class="text-dark align-middle" data-label="Request">
                      <div class="fw-semibold history-request-title mb-1 text-pink">{{ \Illuminate\Support\Str::limit($requestSummary, 120) }}</div>
                      <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="history-meta-badge"><i class="bi bi-tag"></i>{{ $sourceLabel }}</span>
                        <span class="history-meta-badge"><i class="bi bi-journal-text"></i>{{ $actionLabel }}</span>
                      </div>
                      <div class="history-request-extra">
                      @if($item->catalog)
                      <div class="small text-muted">{{ trim(($item->catalog->author ?? '') . ' ' . ($item->catalog->call_number ?? '')) }}</div>
                      @if($item->action === 'borrow' && $item->return_due_date)
                      <div class="small text-muted">Return due: {{ $item->return_due_date->format('M d, Y') }}</div>
                      @endif
                      @elseif($item->program_strand_grade_level)
                      <div class="small text-muted">{{ $item->program_strand_grade_level }}</div>
                      @if($item->action === 'borrow' && $item->return_due_date)
                      <div class="small text-muted">Return due: {{ $item->return_due_date->format('M d, Y') }}</div>
                      @endif
                      @endif
                      @if($item->catalog)
                      <a href="{{ route('catalogs.show', $item->catalog->id) }}" class="history-inline-link">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Open catalog</span>
                      </a>
                      @endif
                      </div>
                    </td>
                    <td class="text-center align-middle" data-label="Status">
                      <span class="status-badge {{ $statusClass }}"><i class="bi {{ $statusIcon }}"></i>{{ $statusText }}</span>
                    </td>
                    <td class="text-center text-dark small align-middle" data-label="Submitted">
                      <div class="history-date-block">
                        <div>{{ optional($item->created_at)->format('M d, Y') }}</div>
                        <div class="text-muted">{{ optional($item->created_at)->format('h:i A') }}</div>
                      </div>
                    </td>
                    <td class="align-middle" data-label="Updates">
                      <div class="history-note small">
                        <span class="history-note-label">Timeline</span>
                        @if($item->status === 'canceled')
                        <div class="history-note-line">This accepted request was canceled by the library.</div>
                        @if($item->decision_reason)
                        <div class="history-note-line">Reason: {{ $item->decision_reason }}</div>
                        @endif
                        @elseif($item->status === 'rejected')
                        <div class="history-note-line">{{ $item->decision_reason ?: 'This request was not approved by the library.' }}</div>
                        @elseif($item->returned_at && $item->return_due_date)
                        <div class="history-note-line">Returned on {{ $item->returned_at->format('M d, Y h:i A') }}.</div>
                        <div class="history-note-line">Due date was {{ $item->return_due_date->format('M d, Y') }}.</div>
                        @elseif($item->borrowed_at && $item->return_due_date)
                        <div class="history-note-line">Borrowed on {{ $item->borrowed_at->format('M d, Y h:i A') }}.</div>
                        <div class="history-note-line">Return on {{ $item->return_due_date->format('M d, Y') }}.</div>
                        @elseif($item->returned_at)
                        <div class="history-note-line">Returned on {{ $item->returned_at->format('M d, Y h:i A') }}.</div>
                        @elseif($item->borrowed_at)
                        <div class="history-note-line">Borrowed on {{ $item->borrowed_at->format('M d, Y h:i A') }}.</div>
                        @elseif($item->response_sent_at)
                        <div class="history-note-line">{{ $item->action === 'scanning' ? 'Scanning completed' : 'Completed' }} on {{ $item->response_sent_at->format('M d, Y h:i A') }}.</div>
                        @elseif($item->processed_at && $item->status === 'accepted')
                        <div class="history-note-line">Approved on {{ $item->processed_at->format('M d, Y h:i A') }}.</div>
                        <div class="history-note-line">Waiting for release or completion.</div>
                        @else
                        <div class="history-note-line">Waiting for librarian review.</div>
                        @endif
                      </div>
                    </td>
                    <td class="text-center align-middle history-action-cell" data-label="Return Date">
                      @if($item->returned_at)
                      <div class="history-date-card">
                        <span class="history-date-card-label">Returned On</span>
                        <span class="history-date-card-value">{{ $item->returned_at->format('M d, Y') }}</span>
                        <span class="history-date-card-sub">{{ $item->returned_at->format('h:i A') }}</span>
                      </div>
                      @elseif($item->action === 'borrow' && $item->return_due_date)
                      <div class="history-date-card">
                        <span class="history-date-card-label">Return Due</span>
                        <span class="history-date-card-value">{{ $item->return_due_date->format('M d, Y') }}</span>
                        @if($item->borrowed_at)
                        <span class="history-date-card-sub">Borrowed {{ $item->borrowed_at->format('M d') }}</span>
                        @endif
                      </div>
                      @else
                      <span class="text-muted small">Not set</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
              {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('footer')
</body>

</html>