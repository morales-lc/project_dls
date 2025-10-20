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
    </ul>
    <form class="d-flex mb-3" method="GET">
      <input name="email" value="{{ request('email') }}" class="form-control me-2" placeholder="filter by email">
      <button class="btn btn-outline-secondary">Filter</button>
    </form>
    </div>

  <div class="list-group">
    @foreach($items as $it)
      <div class="list-group-item d-flex justify-content-between align-items-start" data-id="{{ $it->id }}">
        <div class="flex-grow-1 lira-row" role="button" tabindex="0" data-item="{{ base64_encode($it->toJson()) }}">
          <div><strong>{{ $it->first_name }} {{ $it->last_name }}</strong> <small class="text-muted">({{ $it->email }})</small></div>
          <div class="text-muted">{{ $it->created_at->toDayDateTimeString() }} — {{ $it->action }}</div>
          <div class="mt-2">{{ $it->for_borrow_scan }}</div>
        </div>
        <div class="text-end ms-3" style="min-width: 180px;">
          @php
            $badge = 'secondary';
            if ($it->status === 'pending') $badge = 'warning';
            elseif ($it->status === 'accepted') $badge = 'success';
            elseif ($it->status === 'rejected') $badge = 'danger';
          @endphp
          <div class="mb-2"><span class="badge bg-{{ $badge }}">{{ ucfirst($it->status ?? 'pending') }}</span></div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary lira-row" data-item="{{ base64_encode($it->toJson()) }}">View</button>
            <form class="lira-delete-form" method="POST" action="{{ route('lira.destroy', $it->id) }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
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

    <div class="mt-3">{{ $items->links() }}</div>
</div>

@endsection

@push('management-scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const rows = document.querySelectorAll('.lira-row');
  const modal = new bootstrap.Modal(document.getElementById('liraDetailsModal'));
  const details = document.getElementById('liraDetailsHtml');
  const decisionForm = document.getElementById('decisionForm');

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
    modal.show();
  }));

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
