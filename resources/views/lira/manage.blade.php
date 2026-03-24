@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
  /* Hover pop-out effect adapted from ALINET for LiRA list items */
  .list-group .list-group-item.lira-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }
  .list-group .list-group-item.lira-item:hover {
    background-color: #fff6f9; /* soft pink tint */
    transform: scale(1.02); /* slight pop-out */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12); /* floating look */
    position: relative;
    z-index: 5;
  }
  .lira-row { cursor: pointer; }

  /* Optional: pink-themed title styling similar to ALINET */
  .panel-title-pink {
    letter-spacing: 1px;
    color: #d81b60;
    font-size: 1.75rem;
  }

  .lira-richtext-preview ol,
  .lira-richtext-preview ul,
  .lira-detail-richtext ol,
  .lira-detail-richtext ul {
    margin-bottom: .5rem;
    padding-left: 1.4rem;
  }

  .lira-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: .9rem;
  }

  .lira-detail-card {
    border: 1px solid #f7d4e4;
    border-radius: .85rem;
    background: linear-gradient(180deg, #fff, #fff8fc);
    padding: .75rem .85rem;
  }

  .lira-detail-label {
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9a6b83;
    margin-bottom: .15rem;
  }

  .lira-detail-value {
    color: #2f2f2f;
    font-size: .95rem;
    word-break: break-word;
  }

  .lira-details-header {
    border: 1px solid #f7d4e4;
    border-radius: .95rem;
    background: linear-gradient(90deg, #fff7fb 0%, #fff 100%);
    padding: .9rem;
    margin-bottom: .9rem;
  }

  .lira-details-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #d81b60;
  }

  .lira-detail-meta {
    font-size: .86rem;
    color: #7d6874;
  }

  .lira-status-badge {
    font-size: .74rem;
    border-radius: 999px;
    padding: .35rem .6rem;
  }

  .lira-inventory-badge-lg {
    font-size: 1rem;
    font-weight: 700;
    border-radius: 999px;
    padding: .55rem .9rem;
  }

  .lira-manual-check-note {
    border: 1px solid #ffd08a;
    background: #fff8eb;
    color: #8a5a00;
    border-radius: .75rem;
    padding: .65rem .75rem;
    font-size: .9rem;
  }

  #liraDetailsModal .modal-content {
    border: 1px solid #ffd7e8;
    border-radius: 1rem;
    box-shadow: 0 16px 34px rgba(216, 27, 96, 0.15);
  }

  #liraDetailsModal .modal-header {
    border-bottom-color: #f9d9e8;
    background: linear-gradient(90deg, #fff6fb, #fff);
  }

  #liraDetailsModal .modal-footer {
    border-top-color: #f9d9e8;
    background: #fffafb;
  }
</style>
@endpush

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
  <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1400px; background: #fff;">
    <!-- Success/Status Messages -->
    @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
      <h2 class="fw-bold mb-0 panel-title-pink">LiRA Requests Management</h2>
    </div>

    @php
      $base = url()->current();
      $qs = request()->except(['status','page']);
      $build = function($status = null) use ($base, $qs) {
        $q = $qs;
        if ($status !== null && $status !== '') $q['status'] = $status;
        return $base . (count($q) ? ('?'.http_build_query($q)) : '');
      };
    @endphp

    <div class="card p-3 mb-3 shadow-sm rounded-3">
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a data-status="" class="nav-link {{ request('status')=='' || is_null(request('status')) ? 'active' : '' }}" href="{{ $build(null) }}">All</a></li>
        <li class="nav-item"><a data-status="pending" class="nav-link {{ request('status')=='pending' ? 'active' : '' }}" href="{{ $build('pending') }}">Pending</a></li>
        <li class="nav-item"><a data-status="accepted" class="nav-link {{ request('status')=='accepted' ? 'active' : '' }}" href="{{ $build('accepted') }}">Accepted</a></li>
        <li class="nav-item"><a data-status="rejected" class="nav-link {{ request('status')=='rejected' ? 'active' : '' }}" href="{{ $build('rejected') }}">Rejected</a></li>
        <li class="nav-item"><a data-status="awaiting_response" class="nav-link {{ request('status')=='awaiting_response' ? 'active' : '' }}" href="{{ $build('awaiting_response') }}"> Accepted Request to Process </a></li>
        <li class="nav-item"><a data-status="borrowed" class="nav-link {{ request('status')=='borrowed' ? 'active' : '' }}" href="{{ $build('borrowed') }}">Borrowed</a></li>
        <li class="nav-item"><a data-status="returned" class="nav-link {{ request('status')=='returned' ? 'active' : '' }}" href="{{ $build('returned') }}">Returned</a></li>
      </ul>
      <form id="liraFilterForm" class="row g-2 align-items-end mb-0" method="GET">
        <div class="col-sm-6 col-md-3">
          <label class="form-label mb-1">Email</label>
          <input name="email" value="{{ request('email') }}" class="form-control" placeholder="filter by email">
        </div>
        <div class="col-sm-6 col-md-3">
          <label class="form-label mb-1">Date filter</label>
          <select name="date_filter" id="date_filter" class="form-select">
            @php $df = request('date_filter', ''); @endphp
            <option value="" {{ $df=='' ? 'selected' : '' }}>All dates</option>
            <option value="today" {{ $df=='today' ? 'selected' : '' }}>Today</option>
            <option value="month" {{ $df=='month' ? 'selected' : '' }}>Month & Year</option>
            <option value="range" {{ $df=='range' ? 'selected' : '' }}>Select date</option>
          </select>
        </div>
        <!-- Month & Year: copy analytics behavior with separate Year and Month selects -->
        <div class="col-sm-6 col-md-2 df-month" style="display:none;">
          <label class="form-label mb-1">Year</label>
          <select name="year" class="form-select">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
              <option value="{{ $y }}" {{ (int)request('year', (int)date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
        <div class="col-sm-6 col-md-2 df-month" style="display:none;">
          <label class="form-label mb-1">Month</label>
          <select name="month" class="form-select">
            @for($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}" {{ (int)request('month', (int)date('n')) === $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endfor
          </select>
        </div>
        <div class="col-sm-6 col-md-3 df-range" style="display:none;">
          <label class="form-label mb-1">From</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-sm-6 col-md-3 df-range" style="display:none;">
          <label class="form-label mb-1">To</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-12 col-md-auto">
          <div class="d-flex gap-2">
            <button class="btn btn-dark" type="submit">Filter</button>
            <a href="{{ url()->current() }}" class="btn btn-pink" style="background:#fcb6d0; color:#d81b60; border-color:#fcb6d0;">Reset</a>
          </div>
        </div>
      </form>
    </div>

    <div class="card p-3 shadow-sm rounded-3">
      @php
        $exportBase = route('lira.export.xlsx');
        $params = request()->all(); unset($params['page']);
        $exportHref = $exportBase . (count($params) ? ('?'.http_build_query($params)) : '');
      @endphp
      <div class="d-flex justify-content-end mb-2">
        <div class="btn-group">
          <a id="exportXlsxLink" data-base="{{ route('lira.export.xlsx') }}" href="{{ $exportHref }}" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-excel"></i> Export current tab
          </a>
          <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a id="exportXlsxAllLink" class="dropdown-item" href="#">Export all tabs (7 sheets)</a></li>
          </ul>
        </div>
      </div>
      <div id="liraListContainer">
        @include('lira.partials.list', ['items' => $items])
      </div>
    </div>
  </div>

    <!-- Details Modal -->
    <div class="modal fade" id="liraDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">LiRA Request</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="liraDetailsHtml"></div>
          </div>
          <div class="modal-footer">
            <form id="decisionForm" method="POST" action="" class="w-100">
                @csrf
                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                <div class="mb-2">
                  <label for="decision_reason" class="form-label">Reason for rejection (optional unless rejecting)</label>
                  <textarea id="decision_reason" name="decision_reason" class="form-control" rows="2" placeholder="Provide a short reason if rejecting..."></textarea>
                </div>
                <div class="d-flex justify-content-between">
                  <div id="decisionNotice" class="text-muted small"></div>
                  <div>
                    <button type="submit" name="decision" value="accepted" class="btn btn-success">Accept</button>
                    <button type="submit" name="decision" value="rejected" class="btn btn-danger">Reject</button>
                  </div>
                </div>
            </form>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Respond Modal (separate) -->
    <div class="modal fade" id="liraRespondModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Send Response to Requester</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="liraRespondInfo" class="mb-3 text-muted small"></div>
            <form id="respondForm" method="POST" action="">
              @csrf
              <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
              <div class="mb-2">
                <label for="response_subject" class="form-label">Email subject</label>
                <input id="response_subject" name="response_subject" class="form-control" placeholder="Response to your LiRA request" maxlength="255">
              </div>
              <div class="mb-2">
                <label for="response_message" class="form-label">Message to requester</label>
                <textarea id="response_message" name="response_message" class="form-control" rows="6" placeholder="Write your response..."></textarea>
              </div>
              <div id="manualCopyCheckWrap" class="d-none">
                <div class="lira-manual-check-note mb-2">
                  This catalog has no copies count in the system. Please verify availability manually in the library before sending a response.
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="manual_copy_check_confirmed" id="manual_copy_check_confirmed" value="1">
                  <label class="form-check-label" for="manual_copy_check_confirmed">I have manually verified copy availability in the library.</label>
                </div>
              </div>
            </form>
            <div id="respondNotice" class="text-muted small mt-1"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" form="respondForm" class="btn btn-primary">Send Response</button>
          </div>
        </div>
      </div>
    </div>

  <!-- pagination is rendered inside liraListContainer -->
</div>

@endsection

@push('management-scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const modal = new bootstrap.Modal(document.getElementById('liraDetailsModal'));
  const details = document.getElementById('liraDetailsHtml');
  const decisionForm = document.getElementById('decisionForm');
  const respondForm = document.getElementById('respondForm');
  const respondModal = new bootstrap.Modal(document.getElementById('liraRespondModal'));
  const listContainer = document.getElementById('liraListContainer');

  // Helper to show success messages
  function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
      <i class="bi bi-check-circle-fill me-2"></i>${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => {
      alertDiv.classList.remove('show');
      setTimeout(() => alertDiv.remove(), 150);
    }, 4000);
  }

  // Check for URL parameter success message (for redirects after form submissions)
  const urlParams = new URLSearchParams(window.location.search);
  const statusMsg = urlParams.get('success_msg');
  if (statusMsg) {
    showSuccessMessage(decodeURIComponent(statusMsg));
    // Clean URL
    urlParams.delete('success_msg');
    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
    history.replaceState({}, '', newUrl);
  }

  function bindRowHandlers(scope) {
    scope.querySelectorAll('.lira-row').forEach(r => r.addEventListener('click', onRowClick));
    scope.querySelectorAll('.lira-respond-btn').forEach(b => b.addEventListener('click', onRespondClick));
    scope.querySelectorAll('.lira-return-form').forEach(form => form.addEventListener('submit', onReturnSubmit));
    scope.querySelectorAll('.pagination a').forEach(a => a.addEventListener('click', onPaginateClick));
    scope.querySelectorAll('.lira-delete-form').forEach(form => form.addEventListener('submit', onDeleteSubmit));
  }

  async function loadList(url) {
    const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const html = await resp.text();
    listContainer.innerHTML = html;
    bindRowHandlers(listContainer);
  }

  function onTabClick(e) {
    e.preventDefault();
    // Update active tab styling immediately
    document.querySelectorAll('.nav-tabs .nav-link').forEach(a => a.classList.remove('active'));
    this.classList.add('active');
    const url = this.href;
    // Ensure export links reflect the newly active tab immediately
    if (typeof refreshExportLink === 'function') {
      refreshExportLink();
    }
    history.replaceState({}, '', url);
    loadList(url);
  }

  function onPaginateClick(e) {
    e.preventDefault();
    const url = this.href;
    history.replaceState({}, '', url);
    // Keep export link in sync with current active tab + filters on pagination
    if (typeof refreshExportLink === 'function') {
      refreshExportLink();
    }
    loadList(url);
  }

  function onRowClick(e){
    if (e.target.closest('form')) return;
    let item;
    try {
      const raw = this.getAttribute('data-item') || '';
      item = JSON.parse(atob(raw));
    } catch(e) {
      console.error('Failed to parse LiRA row data:', e);
      return;
    }
    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
    const sanitizeRich = (v) => {
      const raw = String(v ?? '');
      return raw
        .replace(/<(?!\/?(strong|b|em|ol|ul|li|br|p)\b)[^>]*>/gi, '')
        .replace(/on\w+\s*=\s*(['"]).*?\1/gi, '');
    };
    const fullName = [item.first_name, item.middle_name, item.last_name].filter(Boolean).join(' ');
    const statusText = item.loan_status === 'borrowed'
      ? 'Borrowed'
      : item.loan_status === 'returned'
        ? 'Returned'
        : (item.status === 'accepted' && item.response_sent_at)
          ? 'Responded'
          : (item.status || 'Pending');
    const statusClass = statusText === 'Accepted'
      ? 'bg-success'
      : statusText === 'Rejected'
        ? 'bg-danger'
        : statusText === 'Responded'
          ? 'bg-info text-dark'
          : statusText === 'Borrowed'
            ? 'bg-primary'
            : statusText === 'Returned'
              ? 'bg-success'
              : 'bg-warning text-dark';
    const assistance = Array.isArray(item.assistance_types) ? item.assistance_types.join(', ') : (item.assistance_types || '-');
    const resources = Array.isArray(item.resource_types) ? item.resource_types.join(', ') : (item.resource_types || '-');
    const videos = Array.isArray(item.for_videos) ? item.for_videos.join(', ') : (item.for_videos || '-');
    const borrowScan = sanitizeRich(item.for_borrow_scan || '-');
    const hasCatalogInventory = item.catalog && item.catalog.copies_count !== null && item.catalog.copies_count !== undefined;
    const totalCopies = hasCatalogInventory ? Number(item.catalog.copies_count) : null;
    const borrowedCopies = hasCatalogInventory ? Number(item.catalog.borrowed_count || 0) : null;
    const leftCopies = hasCatalogInventory ? Math.max(totalCopies - borrowedCopies, 0) : null;
    const leftBadgeClass = leftCopies > 0 ? 'bg-success-subtle text-success border' : 'bg-danger-subtle text-danger border';

    let html = `
      <div class="lira-details-header d-flex justify-content-between align-items-start gap-2">
        <div>
          <div class="lira-details-name">${esc(fullName || 'No name')}</div>
          <div class="lira-detail-meta">${esc(item.email || '-')}</div>
        </div>
        <span class="badge lira-status-badge ${statusClass}">${esc(statusText)}</span>
      </div>

      <div class="lira-detail-grid">
        <div class="lira-detail-card"><div class="lira-detail-label">Designation</div><div class="lira-detail-value">${esc(item.designation || '-')}</div></div>
        <div class="lira-detail-card"><div class="lira-detail-label">Department</div><div class="lira-detail-value">${esc(item.department || '-')}</div></div>
        <div class="lira-detail-card"><div class="lira-detail-label">Action</div><div class="lira-detail-value">${esc(item.action || '-')}</div></div>
        <div class="lira-detail-card"><div class="lira-detail-label">Submitted</div><div class="lira-detail-value">${esc(item.created_at || '-')}</div></div>
      </div>

      <div class="lira-detail-grid mt-2">
        <div class="lira-detail-card"><div class="lira-detail-label">Assistance Types</div><div class="lira-detail-value">${esc(assistance)}</div></div>
        <div class="lira-detail-card"><div class="lira-detail-label">Resource Types</div><div class="lira-detail-value">${esc(resources)}</div></div>
      </div>

      <div class="lira-detail-grid mt-2">
        <div class="lira-detail-card"><div class="lira-detail-label">Titles/Topics</div><div class="lira-detail-value">${esc(item.titles_of || '-')}</div></div>
        <div class="lira-detail-card"><div class="lira-detail-label">List of References Info</div><div class="lira-detail-value">${esc(item.for_list || '-')}</div></div>
      </div>

      <div class="lira-detail-card mt-2">
        <div class="lira-detail-label">Copies Availability</div>
        <div class="lira-detail-value">
          ${hasCatalogInventory
            ? `<span class="badge lira-inventory-badge-lg bg-secondary-subtle text-dark border me-1">Total: ${esc(totalCopies)}</span>
               <span class="badge lira-inventory-badge-lg bg-primary-subtle text-primary border me-1">Borrowed: ${esc(borrowedCopies)}</span>
               <span class="badge lira-inventory-badge-lg ${leftBadgeClass}">Left: ${esc(leftCopies)}</span>`
            : item.action === 'borrow'
              ? '<span class="badge lira-inventory-badge-lg bg-warning-subtle text-warning border">Manual Check Required</span>'
              : '<span class="text-muted">N/A (no catalog mapping)</span>'}
        </div>
      </div>

      <div class="lira-detail-card mt-2">
        <div class="lira-detail-label">Borrow/Scan Details</div>
        <div class="lira-detail-value lira-detail-richtext">${borrowScan || '-'}</div>
      </div>

      <div class="lira-detail-card mt-2">
        <div class="lira-detail-label">Videos</div>
        <div class="lira-detail-value">${esc(videos)}</div>
      </div>
    `;
    if (item.decision_reason) {
      html += `<div class="lira-detail-card mt-2"><div class="lira-detail-label">Decision Reason</div><div class="lira-detail-value">${esc(item.decision_reason)}</div></div>`;
    }
    if (item.borrowed_at) {
      html += `<div class="lira-detail-card mt-2"><div class="lira-detail-label">Borrowed At</div><div class="lira-detail-value">${esc(item.borrowed_at)}</div></div>`;
    }
    if (item.returned_at) {
      html += `<div class="lira-detail-card mt-2"><div class="lira-detail-label">Returned At</div><div class="lira-detail-value">${esc(item.returned_at)}</div></div>`;
    }
    details.innerHTML = html;
    decisionForm.action = '/lira/' + item.id + '/decide';
    // Ensure we return to the current filtered/paginated URL
    const retInput = decisionForm.querySelector('input[name="return_url"]');
    if (retInput) retInput.value = window.location.href;
    const acceptBtn = decisionForm.querySelector('button[name="decision"][value="accepted"]');
    const rejectBtn = decisionForm.querySelector('button[name="decision"][value="rejected"]');
    const reasonField = document.getElementById('decision_reason');
    const isPending = (item.status === 'pending' || !item.status);
    acceptBtn.disabled = !isPending;
    rejectBtn.disabled = !isPending;
    reasonField.disabled = !isPending;
    const notice = document.getElementById('decisionNotice');
    notice.textContent = isPending ? '' : 'This request has already been processed and can no longer be changed.';
    modal.show();
  }

  function onRespondClick(e){
    e.stopPropagation();
    let item;
    try {
      const raw = this.getAttribute('data-item') || '';
      item = JSON.parse(atob(raw));
    } catch(err) {
      console.error('Failed to parse LiRA row data:', err);
      return;
    }
    respondForm.action = '/lira/' + item.id + '/respond';
    // Ensure we return to the current filtered/paginated URL
    const retInput = respondForm.querySelector('input[name="return_url"]');
    if (retInput) retInput.value = window.location.href;
    const subjectField = document.getElementById('response_subject');
    const messageField = document.getElementById('response_message');
    const respondNotice = document.getElementById('respondNotice');
    const manualWrap = document.getElementById('manualCopyCheckWrap');
    const manualCheckbox = document.getElementById('manual_copy_check_confirmed');
    const info = document.getElementById('liraRespondInfo');
    const parts = [];
    parts.push(`<strong>${item.first_name} ${item.last_name}</strong> <span class="text-muted">(${item.email})</span>`);
    if (item.for_borrow_scan) parts.push(`<div class="mt-1">${item.for_borrow_scan}</div>`);
    if (item.titles_of) parts.push(`<div class="mt-1"><em>${item.titles_of}</em></div>`);
    info.innerHTML = parts.join('');
    const canRespond = (item.status === 'accepted') && !item.response_sent_at;
    const requiresManualCheck = item.action === 'borrow' && item.catalog && (item.catalog.copies_count === null || item.catalog.copies_count === undefined);
    subjectField.value = 'Response to your LiRA request';
    const submittedStr = item.created_at ? new Date(item.created_at).toLocaleString() : '';
    messageField.value = `This is a response to your LiRA request submitted on ${submittedStr}.\n\n[Type your message here]\n\nBest regards,\nLC Learning Commons`;
    const submitBtn = document.querySelector('#liraRespondModal button[type="submit"]');
    if (manualCheckbox) manualCheckbox.checked = false;
    if (manualWrap) manualWrap.classList.toggle('d-none', !requiresManualCheck);
    submitBtn.disabled = !canRespond;
    subjectField.disabled = !canRespond;
    messageField.disabled = !canRespond;
    respondNotice.textContent = canRespond ? '' : (item.response_sent_at ? ('A response was already sent on ' + item.response_sent_at + '.') : 'Responses can be sent only after accepting the request.');
    respondModal.show();
  }

  function onDeleteSubmit(e){
    e.preventDefault();
    if (!confirm('Delete this LiRA request? This action cannot be undone.')) return;
    const form = e.target;
    const url = form.action;
    const row = form.closest('[data-id]');
    const token = (form.querySelector('input[name="_token"]').value) || (document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    fetch(url, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    }).then(async res => {
      let data;
      try { data = await res.json(); } catch(_) { data = { success: res.ok }; }
      if (data && data.success) {
        if (row) row.remove();
        const modalEl = document.getElementById('liraDetailsModal');
        const instance = bootstrap.Modal.getInstance(modalEl);
        if (instance) instance.hide();
        showSuccessMessage(data.message || 'LiRA request deleted successfully.');
      } else {
        const msg = (data && data.message) || `Failed to delete (status ${res.status}). Attempting normal delete...`;
        alert(msg);
        form.submit();
      }
    }).catch(err => {
      console.error(err);
      alert('Failed to delete request via AJAX. Attempting normal delete...');
      form.submit();
    });
  }

  function onReturnSubmit(e) {
    e.preventDefault();
    if (!confirm('Mark this borrowed request as returned?')) return;

    const form = e.target;
    const token = (form.querySelector('input[name="_token"]').value) || (document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    }).then(async res => {
      const data = await res.json().catch(() => ({ success: false }));
      if (res.ok && data.success !== false) {
        showSuccessMessage(data.message || 'Request marked as returned.');
        loadList(window.location.href);
      } else {
        alert(data.message || 'Failed to mark as returned.');
      }
    }).catch(err => {
      console.error(err);
      form.submit();
    });
  }

  // Intercept tab clicks to load via AJAX
  document.querySelectorAll('.nav-tabs a').forEach(a => {
    a.addEventListener('click', onTabClick);
  });

  // Bind initial handlers for server-rendered list
  bindRowHandlers(listContainer);

  // Date filter UI toggling (show Year+Month or From/To when selected)
  const filterForm = document.getElementById('liraFilterForm');
  const dateFilterSel = document.getElementById('date_filter');
  function applyDateFilterVisibility() {
    const val = (dateFilterSel && dateFilterSel.value) || '';
    const showMonth = (val === 'month');
    const showRange = (val === 'range');
    document.querySelectorAll('.df-month').forEach(el => {
      el.style.display = showMonth ? '' : 'none';
      el.querySelectorAll('select,input').forEach(ctrl => ctrl.disabled = !showMonth);
    });
    document.querySelectorAll('.df-range').forEach(el => {
      el.style.display = showRange ? '' : 'none';
      el.querySelectorAll('select,input').forEach(ctrl => ctrl.disabled = !showRange);
    });
  }
  if (dateFilterSel) {
    dateFilterSel.addEventListener('change', applyDateFilterVisibility);
    // initialize on load
    applyDateFilterVisibility();
  }

  // Build query string from current form values
  function buildQueryFromForm(form) {
    const params = new URLSearchParams();
    if (!form) return params.toString();
    Array.from(new FormData(form).entries()).forEach(([k, v]) => {
      if (v !== null && v !== undefined && String(v).trim() !== '') params.append(k, v);
    });
    return params.toString();
  }

  // Keep tab links in sync with current filters
  function refreshTabHrefs() {
    const base = window.location.pathname;
    const qs = filterForm ? buildQueryFromForm(filterForm) : '';
    document.querySelectorAll('.nav-tabs a.nav-link').forEach(a => {
      const status = a.getAttribute('data-status') || '';
      const p = new URLSearchParams(qs);
      if (status) p.set('status', status); else p.delete('status');
      p.delete('page');
      const qstr = p.toString();
      a.href = base + (qstr ? ('?' + qstr) : '');
    });
    refreshExportLink();
  }
  refreshTabHrefs();

  function refreshExportLink() {
    const link = document.getElementById('exportXlsxLink');
    const allLink = document.getElementById('exportXlsxAllLink');
    if (!link) return;
    const p = new URLSearchParams(filterForm ? buildQueryFromForm(filterForm) : '');
    const activeTab = document.querySelector('.nav-tabs .nav-link.active');
    if (activeTab) {
      const st = activeTab.getAttribute('data-status') || '';
      if (st) p.set('status', st); else p.delete('status');
    }
    p.delete('page');
    const base = link.getAttribute('data-base') || (link.href ? link.href.split('?')[0] : '');
    link.href = base + (p.toString() ? ('?' + p.toString()) : '');
    if (allLink) {
      const p2 = new URLSearchParams(p.toString());
      p2.set('all_tabs', '1');
      allLink.href = base + (p2.toString() ? ('?' + p2.toString()) : '');
    }
  }

  // Centralized updater: build URL from form + active tab, update history, tabs, and list
  function updateListFromForm() {
    const base = window.location.pathname;
    const qs = buildQueryFromForm(filterForm);
    const activeTab = document.querySelector('.nav-tabs .nav-link.active');
    const p = new URLSearchParams(qs);
    if (activeTab) {
      const st = activeTab.getAttribute('data-status') || '';
      if (st) p.set('status', st); else p.delete('status');
    }
    p.delete('page');
    const url = base + (p.toString() ? ('?' + p.toString()) : '');
    history.replaceState({}, '', url);
    refreshTabHrefs();
    refreshExportLink();
    loadList(url);
  }

  // Intercept filter form submit to use AJAX and persist URL + tab hrefs
  if (filterForm) {
    filterForm.addEventListener('submit', function(e){
      e.preventDefault();
      updateListFromForm();
    });
  }

  // Auto-apply: change listeners
  const emailInput = filterForm ? filterForm.querySelector('input[name="email"]') : null;
  function debounce(fn, delay = 500) {
    let t; return function(...args){ clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
  }
  if (emailInput) {
    emailInput.addEventListener('input', debounce(() => {
      updateListFromForm();
    }, 500));
  }

  if (dateFilterSel) {
    dateFilterSel.addEventListener('change', function(){
      applyDateFilterVisibility();
      // Clear irrelevant fields by disabling them in visibility fn; then update
      updateListFromForm();
    });
  }

  // Year/Month selects auto-apply
  document.querySelectorAll('.df-month select').forEach(sel => {
    sel.addEventListener('change', function(){
      if (dateFilterSel && dateFilterSel.value === 'month') updateListFromForm();
    });
  });
  // Date range inputs auto-apply
  document.querySelectorAll('.df-range input[type="date"]').forEach(inp => {
    inp.addEventListener('change', function(){
      if (dateFilterSel && dateFilterSel.value === 'range') updateListFromForm();
    });
  });

  // Require reason when rejecting
  decisionForm.addEventListener('submit', function(e){
    e.preventDefault(); // Prevent default immediately
    
    const btn = document.activeElement; // which button submitted
    
    // Validate button exists and has required attributes
    if (!btn || btn.name !== 'decision' || !btn.value) {
      alert('Please click Accept or Reject button.');
      return;
    }
    
    const decision = btn.value;
    
    // Check reason is provided when rejecting
    if (decision === 'rejected') {
      const reason = document.getElementById('decision_reason').value.trim();
      if (!reason) {
        alert('Please provide a reason for rejection.');
        document.getElementById('decision_reason').focus();
        return;
      }
    }
    
    // Build FormData and ensure decision is included
    const formData = new FormData(decisionForm);
    formData.set('decision', decision); // Explicitly set the decision value
    
    fetch(decisionForm.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    }).then(async res => {
      const data = await res.json().catch(() => ({ success: false }));
      if (res.ok && data.success !== false) {
        modal.hide();
        const message = decision === 'accepted' 
          ? 'Request accepted successfully. An email notification has been sent to the requester.'
          : 'Request rejected successfully. An email notification has been sent to the requester.';
        showSuccessMessage(message);
        // Reload the current list
        loadList(window.location.href);
      } else {
        alert(data.message || 'Failed to process decision. Please try again.');
      }
    }).catch(err => {
      console.error('Error submitting decision:', err);
      // Fallback to normal form submission
      decisionForm.submit();
    });
  });

  // Intercept respond form submission
  respondForm.addEventListener('submit', function(e){
    e.preventDefault();
    const manualWrap = document.getElementById('manualCopyCheckWrap');
    const manualCheckbox = document.getElementById('manual_copy_check_confirmed');
    if (manualWrap && !manualWrap.classList.contains('d-none') && manualCheckbox && !manualCheckbox.checked) {
      alert('Please confirm manual copy verification before sending the response.');
      manualCheckbox.focus();
      return;
    }
    const formData = new FormData(respondForm);
    fetch(respondForm.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    }).then(async res => {
      const data = await res.json().catch(() => ({ success: false }));
      if (res.ok && data.success !== false) {
        respondModal.hide();
        showSuccessMessage('Response sent successfully to the requester.');
        // Reload the current list
        loadList(window.location.href);
      } else {
        alert(data.message || 'Failed to send response. Please try again.');
      }
    }).catch(err => {
      console.error('Error submitting response:', err);
      // Fallback to normal form submission
      respondForm.submit();
    });
  });

  
});
</script>
@endpush
