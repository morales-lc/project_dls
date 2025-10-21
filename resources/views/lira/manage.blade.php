@extends('layouts.management')

@push('management-head')
<link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>LiRA Requests</h3>
    @php
      $base = url()->current();
      $qs = request()->except(['status','page']);
      $build = function($status = null) use ($base, $qs) {
        $q = $qs;
        if ($status !== null && $status !== '') $q['status'] = $status;
        return $base . (count($q) ? ('?'.http_build_query($q)) : '');
      };
      $active = request('status', 'all');
    @endphp

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item"><a class="nav-link {{ request('status')=='' || is_null(request('status')) ? 'active' : '' }}" href="{{ $build(null) }}">All</a></li>
      <li class="nav-item"><a class="nav-link {{ request('status')=='pending' ? 'active' : '' }}" href="{{ $build('pending') }}">Pending</a></li>
      <li class="nav-item"><a class="nav-link {{ request('status')=='accepted' ? 'active' : '' }}" href="{{ $build('accepted') }}">Accepted</a></li>
      <li class="nav-item"><a class="nav-link {{ request('status')=='rejected' ? 'active' : '' }}" href="{{ $build('rejected') }}">Rejected</a></li>
      <li class="nav-item"><a class="nav-link {{ request('status')=='awaiting_response' ? 'active' : '' }}" href="{{ $build('awaiting_response') }}">Awaiting Response</a></li>
    </ul>
    <form class="d-flex mb-3" method="GET">
      <input name="email" value="{{ request('email') }}" class="form-control me-2" placeholder="filter by email">
      <button class="btn btn-outline-secondary">Filter</button>
    </form>
    </div>

  <div id="liraListContainer">
    @include('lira.partials.list', ['items' => $items])
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
    const url = this.href;
    history.replaceState({}, '', url);
    loadList(url);
  }

  function onPaginateClick(e) {
    e.preventDefault();
    const url = this.href;
    history.replaceState({}, '', url);
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
    show('Status', item.status);
    show('Submitted', item.created_at);
    if (item.decision_reason) { show('Decision reason', item.decision_reason); }
    html += '</dl>';
    details.innerHTML = html;
    decisionForm.action = '/lira/' + item.id + '/decide';
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

  rows.forEach(r => r.addEventListener('click', function(e){
    // prevent clicks on action buttons from triggering the modal open
    if (e.target.closest('form')) return;
    let item;
    try {
      const raw = this.getAttribute('data-item') || '';
      item = JSON.parse(atob(raw));
    } catch(e) {
      console.error('Failed to parse LiRA row data:', e);
      return;
    }
    // build HTML
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
  // Catalog details moved into example_purposive field -- no separate catalog_* fields
    show('Status', item.status);
    show('Submitted', item.created_at);
    if (item.decision_reason) { show('Decision reason', item.decision_reason); }
    html += '</dl>';
    details.innerHTML = html;
  decisionForm.action = '/lira/' + item.id + '/decide';
    // disable actions if not pending
    const acceptBtn = decisionForm.querySelector('button[name="decision"][value="accepted"]');
    const rejectBtn = decisionForm.querySelector('button[name="decision"][value="rejected"]');
    const reasonField = document.getElementById('decision_reason');
  const isPending = (item.status === 'pending' || !item.status);
    acceptBtn.disabled = !isPending;
    rejectBtn.disabled = !isPending;
    reasonField.disabled = !isPending;
  const notice = document.getElementById('decisionNotice');
  notice.textContent = isPending ? '' : 'This request has already been processed and can no longer be changed.';

  // Response form availability: only for accepted; lock if already responded
    modal.show();
  }));

  // Open Respond modal from row button
  document.querySelectorAll('.lira-respond-btn').forEach(btn => {
    btn.addEventListener('click', function(e){
      e.stopPropagation();
      let item;
      try {
        const raw = this.getAttribute('data-item') || '';
        item = JSON.parse(atob(raw));
      } catch(err) {
        console.error('Failed to parse LiRA row data:', err);
        return;
      }
      // Set form action
      respondForm.action = '/lira/' + item.id + '/respond';
      const subjectField = document.getElementById('response_subject');
      const messageField = document.getElementById('response_message');
      const respondNotice = document.getElementById('respondNotice');
      const info = document.getElementById('liraRespondInfo');
      // show a short summary in the modal
      const parts = [];
      parts.push(`<strong>${item.first_name} ${item.last_name}</strong> <span class="text-muted">(${item.email})</span>`);
      if (item.for_borrow_scan) parts.push(`<div class="mt-1">${item.for_borrow_scan}</div>`);
      if (item.titles_of) parts.push(`<div class="mt-1"><em>${item.titles_of}</em></div>`);
      info.innerHTML = parts.join('');

      const canRespond = (item.status === 'accepted') && !item.response_sent_at;
      // Default subject/body without id and greeting
      subjectField.value = 'Response to your LiRA request';
      const submittedStr = item.created_at ? new Date(item.created_at).toLocaleString() : '';
      messageField.value = `This is a response to your LiRA request submitted on ${submittedStr}.\n\n[Type your message here]\n\nBest regards,\nLC Learning Commons`;
      // Enable/disable
      const submitBtn = document.querySelector('#liraRespondModal button[type="submit"]');
      submitBtn.disabled = !canRespond;
      subjectField.disabled = !canRespond;
      messageField.disabled = !canRespond;
      respondNotice.textContent = canRespond ? '' : (item.response_sent_at ? ('A response was already sent on ' + item.response_sent_at + '.') : 'Responses can be sent only after accepting the request.');
      respondModal.show();
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

  // AJAX delete handler for inline buttons
  document.querySelectorAll('.lira-delete-form').forEach(function(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();
      if (!confirm('Delete this LiRA request? This action cannot be undone.')) return;
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
        try {
          data = await res.json();
        } catch(_) {
          // non-JSON response (e.g., 419/redirect). Construct a basic object.
          data = { success: res.ok };
        }
        if (data && data.success) {
          if (row) row.remove();
          // hide modal if open
          const modalEl = document.getElementById('liraDetailsModal');
          const instance = bootstrap.Modal.getInstance(modalEl);
          if (instance) instance.hide();
          // toast
          const alert = document.createElement('div');
          alert.className = 'alert alert-success position-fixed end-0 m-4 shadow-sm';
          alert.style.zIndex = 1050;
          alert.textContent = data.message || 'Deleted';
          document.body.appendChild(alert);
          setTimeout(() => alert.remove(), 2200);
        } else {
          const msg = (data && data.message) || `Failed to delete (status ${res.status}). Attempting normal delete...`;
          alert(msg);
          // fallback to normal (non-AJAX) submission
          form.submit();
        }
      }).catch(err => {
        console.error(err);
        alert('Failed to delete request via AJAX. Attempting normal delete...');
        form.submit();
      });
    });
  });
});
</script>
@endpush
