<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

  <style>
    /* Reuse pink theme */
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

    .card-header.bg-pink {
      background-color: #ffe3ef !important;
      color: #d81b60 !important;
      font-weight: 700;
      border-bottom: 2px solid #ffd1e3;
    }

    .card-body {
      background: linear-gradient(180deg, #fff 90%, #ffe3ef 100%);
    }

    /* Mobile adjustments */
    @media (max-width: 767.98px) {
      .card {
        margin: 0.4rem;
        border-radius: 1rem;
      }
      .card-body { padding: 1rem; }
    }
  </style>
</head>

<body>
  @include('navbar')

  <div class="d-flex" style="min-height: 80vh; background: #f8f9fa;">
    @include('sidebar')

    <div class="flex-grow-1 d-flex justify-content-center align-items-start py-4">
      <div class="card shadow-lg w-100 border-0" style="max-width: 1100px; border-radius: 1.5rem;">
        <div class="card-header bg-pink d-flex align-items-center justify-content-between" style="border-radius: 1.5rem 1.5rem 0 0;">
          <div class="d-flex align-items-center">
            <i class="bi bi-clock-history fs-3 me-2"></i>
            <span class="fw-bold fs-5">Search History</span>
          </div>
          <button id="clearAllBtn" class="btn btn-sm btn-outline-danger shadow-sm px-3" style="border-radius: 0.7rem;">
            <i class="bi bi-trash"></i> Clear All
          </button>
        </div>

        <div class="card-body">
          @if(isset($histories) && $histories->count())
            <div id="historiesList" class="list-group">
              @foreach($histories as $h)
                <div class="list-group-item d-flex justify-content-between align-items-start" data-id="{{ $h->id }}">
                  <div style="flex:1; min-width:0;">
                    <div class="fw-semibold query-text text-pink">
                      {{ $h->query ?: '(empty query)' }}
                    </div>
                    <input class="form-control form-control-sm d-none edit-input" value="{{ $h->query }}">
                    <div class="small text-muted">{{ $h->created_at->diffForHumans() }}</div>
                  </div>
                  <div class="ms-2 d-flex align-items-center gap-2">

                    <a href="{{ route('catalogs.search', ['q' => $h->query]) }}" class="btn btn-sm btn-outline-primary me-2">Repeat</a>
                    <button class="btn btn-sm btn-outline-danger shadow-sm deleteBtn">
                      <i class="bi bi-x-circle"></i> Delete
                    </button>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #ffe3ef; color: #d81b60; border: 1.5px solid #ffd1e3;">
              <i class="bi bi-clock fs-2 me-2"></i> No search history found.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  @include('footer')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const csrfToken = '{{ csrf_token() }}';

      // Edit toggle (if edit buttons exist)
      document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          const item = btn.closest('.list-group-item');
          item.querySelector('.query-text').classList.toggle('d-none');
          item.querySelector('.edit-input').classList.toggle('d-none');
        });
      });

      // Delete (calls backend)
      document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
          const item = btn.closest('.list-group-item');
          if (!item) return;
          const id = item.getAttribute('data-id');
          if (!id) return;
          if (!confirm('Delete this history item?')) return;
          btn.disabled = true;
          try {
            const res = await fetch("{{ url('/history') }}/" + id, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            });
            const json = await res.json().catch(() => ({}));
            if (res.ok && (json.success || json.success === undefined)) {
              item.style.transition = 'opacity 0.3s ease';
              item.style.opacity = 0;
              setTimeout(() => item.remove(), 300);
            } else {
              alert('Failed to delete history item.');
              btn.disabled = false;
            }
          } catch (err) {
            console.error(err);
            alert('Failed to delete history item.');
            btn.disabled = false;
          }
        });
      });

      // Clear All (calls backend)
      const clearBtn = document.getElementById('clearAllBtn');
      if (clearBtn) {
        clearBtn.addEventListener('click', async () => {
          if (!confirm('Clear all search history? This cannot be undone.')) return;
          clearBtn.disabled = true;
          try {
            const res = await fetch("{{ route('history.clear') }}", {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            });
            const json = await res.json().catch(() => ({}));
            if (res.ok && (json.success || json.success === undefined)) {
              document.getElementById('historiesList')?.remove();
            } else {
              alert('Failed to clear history.');
            }
          } catch (err) {
            console.error(err);
            alert('Failed to clear history.');
          } finally {
            clearBtn.disabled = false;
          }
        });
      }
    });
  </script>
</body>
</html>
