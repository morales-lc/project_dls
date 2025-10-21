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
          $canRespond = ($it->status === 'accepted') && empty($it->response_sent_at);
        @endphp
        <div class="mb-2"><span class="badge bg-{{ $badge }}">{{ ucfirst($it->status ?? 'pending') }}</span></div>
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-sm btn-outline-secondary lira-row" data-item="{{ base64_encode($it->toJson()) }}">View</button>
          <button type="button" class="btn btn-sm btn-primary lira-respond-btn" data-item="{{ base64_encode($it->toJson()) }}" {{ $canRespond ? '' : 'disabled' }}>Respond</button>
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

<div class="mt-3">{{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
