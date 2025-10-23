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
</style>
@endpush

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-5 d-flex flex-column align-items-center justify-content-center">
  <div class="alert-panel-card shadow rounded-4 p-4 w-100" style="max-width: 1400px; background: #fff;">
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
        <li class="nav-item"><a data-status="awaiting_response" class="nav-link {{ request('status')=='awaiting_response' ? 'active' : '' }}" href="{{ $build('awaiting_response') }}">Awaiting Response</a></li>
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
            <li><a id="exportXlsxAllLink" class="dropdown-item" href="#">Export all tabs (5 sheets)</a></li>
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

  function bindRowHandlers(scope) {
    scope.querySelectorAll('.lira-row').forEach(r => r.addEventListener('click', onRowClick));
    scope.querySelectorAll('.lira-respond-btn').forEach(b => b.addEventListener('click', onRespondClick));
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
    let html = '<dl class="row">';
    const show = (k,l) => html += `<dt class="col-sm-4">${k}</dt><dd class="col-sm-8">${l??'-'}</dd>`;
    show('Name', item.first_name + ' ' + (item.middle_name? (item.middle_name + ' ') : '') + item.last_name);
    show('Email', item.email);
    show('Designation', item.designation);
    show('Department', item.department);
    show('Action', item.action);
    show('Assistance types', Array.isArray(item.assistance_types) ? item.assistance_types.join(', ') : (item.assistance_types || ''));
    show('Resource types', Array.isArray(item.resource_types) ? item.resource_types.join(', ') : (item.resource_types || ''));
    show('Titles of', item.titles_of);
    show('For borrow/scan (details)', item.for_borrow_scan);
    show('For list', item.for_list);
    show('For videos', Array.isArray(item.for_videos) ? item.for_videos.join(', ') : (item.for_videos || ''));
    const statusText = (item.status === 'accepted' && item.response_sent_at) ? 'Responded' : (item.status || '');
    show('Status', statusText);
    show('Submitted', item.created_at);
    if (item.decision_reason) { show('Decision reason', item.decision_reason); }
    html += '</dl>';
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
    const info = document.getElementById('liraRespondInfo');
    const parts = [];
    parts.push(`<strong>${item.first_name} ${item.last_name}</strong> <span class="text-muted">(${item.email})</span>`);
    if (item.for_borrow_scan) parts.push(`<div class="mt-1">${item.for_borrow_scan}</div>`);
    if (item.titles_of) parts.push(`<div class="mt-1"><em>${item.titles_of}</em></div>`);
    info.innerHTML = parts.join('');
    const canRespond = (item.status === 'accepted') && !item.response_sent_at;
    subjectField.value = 'Response to your LiRA request';
    const submittedStr = item.created_at ? new Date(item.created_at).toLocaleString() : '';
    messageField.value = `This is a response to your LiRA request submitted on ${submittedStr}.\n\n[Type your message here]\n\nBest regards,\nLC Learning Commons`;
    const submitBtn = document.querySelector('#liraRespondModal button[type="submit"]');
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
        const alert = document.createElement('div');
        alert.className = 'alert alert-success position-fixed end-0 m-4 shadow-sm';
        alert.style.zIndex = 1050;
        alert.textContent = data.message || 'Deleted';
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 2200);
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
    const btn = document.activeElement; // which button submitted
    if (btn && btn.name === 'decision' && btn.value === 'rejected') {
      const reason = document.getElementById('decision_reason').value.trim();
      if (!reason) {
        e.preventDefault();
        alert('Please provide a reason for rejection.');
        document.getElementById('decision_reason').focus();
      }
    }
  });

  
});
</script>
@endpush
