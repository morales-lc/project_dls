<div class="list-group">
  @foreach($items as $it)
    @php
      // Normalize arrays from casts or legacy JSON strings
      $assist = is_array($it->assistance_types) ? $it->assistance_types : (json_decode($it->assistance_types ?? '[]', true) ?: []);
      $resTypes = is_array($it->resource_types) ? $it->resource_types : (json_decode($it->resource_types ?? '[]', true) ?: []);

      $hasResponded = ($it->status === 'accepted') && !empty($it->response_sent_at);
      $canRespond = ($it->status === 'accepted') && empty($it->response_sent_at);

      // Badge color and text
      if ($it->status === 'pending') { $badge = 'warning'; $statusText = 'Pending'; }
      elseif ($it->status === 'rejected') { $badge = 'danger'; $statusText = 'Rejected'; }
      elseif ($hasResponded) { $badge = 'info'; $statusText = 'Responded'; }
      elseif ($it->status === 'accepted') { $badge = 'success'; $statusText = 'Accepted'; }
      else { $badge = 'secondary'; $statusText = ucfirst($it->status ?? ''); }
    @endphp

    <div class="list-group-item lira-item d-flex justify-content-between align-items-start" data-id="{{ $it->id }}">
      <div class="flex-grow-1 lira-row" role="button" tabindex="0" data-item="{{ base64_encode($it->toJson()) }}">
        <div><strong>{{ $it->first_name }} {{ $it->last_name }}</strong> <small class="text-muted">({{ $it->email }})</small></div>
        <div class="text-muted">{{ $it->created_at->toDayDateTimeString() }}</div>
        @if(!empty($assist))
          <div class="text-muted small">Assistance types: {{ implode(', ', $assist) }}</div>
        @endif
        @if(!empty($resTypes))
          <div class="text-muted small">Resource types: {{ implode(', ', $resTypes) }}</div>
        @endif
        @if(!empty($it->for_borrow_scan))
          <div class="mt-2">{{ $it->for_borrow_scan }}</div>
        @endif
      </div>
      <div class="text-end ms-3" style="min-width: 200px;">
        <div class="mb-2"><span class="badge bg-{{ $badge }}">{{ $statusText }}</span></div>
        <div class="d-flex justify-content-end gap-2">
          @if($canRespond)
            <button type="button" class="btn btn-sm btn-primary lira-respond-btn" data-item="{{ base64_encode($it->toJson()) }}">Respond</button>
          @endif
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
