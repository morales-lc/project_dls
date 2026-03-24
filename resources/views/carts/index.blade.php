<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Cart</title>
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

    .table tbody tr {
      background: #fff;
      border-radius: 0.75rem;
      box-shadow: 0 1px 4px rgba(216, 27, 96, 0.08);
      transition: all 0.25s ease;
    }

    .table tbody tr:hover {
      transform: translateY(-2px);
      box-shadow: 0 3px 10px rgba(216, 27, 96, 0.15);
      background-color: #fff8fb !important;
    }

    .table td,
    .table th {
      border: none !important;
      vertical-align: middle;
    }

    .table td .fw-semibold {
      font-size: 1rem;
      color: #c2185b !important;
    }

    .table td .small.text-muted {
      font-size: 0.875rem;
      color: #7a7a7a !important;
    }

    .btn-outline-danger {
      color: #e53935 !important;
      border-color: #ffbaba !important;
      border-radius: 0.8rem;
    }

    .btn-outline-danger:hover {
      background-color: #ffecec !important;
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

    @media (max-width: 767.98px) {
      .card {
        margin: 0.4rem;
        border-radius: 1rem;
      }

      .table-responsive-mobile {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      thead {
        display: none;
      }

      tr {
        display: block;
        border-bottom: 1px solid #ffe3ef;
        margin-bottom: .6rem;
      }

      tr td {
        display: block;
        width: 100%;
        padding: .35rem .5rem;
      }

      .card-body {
        padding: 1rem;
      }

      .btn-outline-pink,
      .btn-outline-danger,
      .btn-pink {
        width: 100%;
        margin-top: 0.3rem;
      }
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
            <i class="bi bi-cart-check-fill fs-3 me-2"></i>
            <span class="fw-bold fs-5">My Cart</span>
          </div>
          @if($cartItems->count())
          <form method="POST" action="{{ route('cart.checkout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-pink">
              <i class="bi bi-bag-check me-1"></i> Checkout to LiRA
            </button>
          </form>
          @endif
        </div>

        <div class="card-body">
          @php
          $catalogItems = $cartItems->filter(fn($c) => $c->cartable_type === \App\Models\Catalog::class);
          $alertItems = $cartItems->filter(fn($c) => $c->cartable_type === \App\Models\AlertBook::class);
          @endphp

          <ul class="nav nav-tabs mb-3" id="cartTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#tab-all" type="button" role="tab">All</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="catalogs-tab" data-bs-toggle="tab" data-bs-target="#tab-catalogs" type="button" role="tab">Catalogs</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="alert-tab" data-bs-toggle="tab" data-bs-target="#tab-alert" type="button" role="tab">Alert Service</button>
            </li>
          </ul>

          @php
          function renderCartRows($collection) {
            foreach ($collection as $ci) {
              echo '<tr style="background: #fff;">';
              echo '<td class="text-capitalize text-center align-middle"><span class="badge bg-pink px-3 py-2"><i class="bi bi-cart3 fs-6 me-1"></i>'.class_basename($ci->cartable_type).'</span></td>';
              echo '<td class="text-dark align-middle">';
              if ($ci->cartable) {
                $item = $ci->cartable;
                $route = null;
                $typeValue = 'catalog';
                if ($ci->cartable_type === \App\Models\Catalog::class) {
                  $route = route('catalogs.show', $item->id);
                  $typeValue = 'catalog';
                } elseif ($ci->cartable_type === \App\Models\AlertBook::class) {
                  $route = $item->pdf_path ? asset('storage/' . $item->pdf_path) : null;
                  $typeValue = 'alert_book';
                }
                echo '<div class="fw-semibold fs-6 mb-1 text-pink">'.htmlspecialchars($item->title ?? 'Untitled').'</div>';
                echo '<div class="small text-muted">'.htmlspecialchars(trim(($item->author ?? '') . ' ' . ($item->call_number ?? ''))).'</div>';
              } else {
                echo '<em class="text-danger">Item removed</em>';
                $typeValue = 'catalog';
              }
              echo '</td>';
              echo '<td class="text-center text-dark small align-middle">'.$ci->created_at->diffForHumans().'</td>';
              echo '<td class="text-center align-middle">';
              echo '<div class="d-flex justify-content-center align-items-center gap-2">';
              if (!empty($route)) {
                echo '<a href="'.$route.'" class="btn btn-sm btn-outline-pink shadow-sm px-3" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open</a>';
              }
              echo '<form action="'.route('cart.toggle').'" method="POST" class="d-inline cart-toggle">'.csrf_field().'<input type="hidden" name="id" value="'.($ci->cartable_id).'">'.'<input type="hidden" name="type" value="'.($typeValue ?? 'catalog').'">'.'<button class="btn btn-sm btn-outline-danger shadow-sm px-3"><i class="bi bi-x-circle"></i> Remove</button></form>';
              echo '</div>';
              echo '</td>';
              echo '</tr>';
            }
          }
          @endphp

          <div class="tab-content" id="cartTabsContent">
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
              @if($cartItems->isEmpty())
              <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #ffe3ef; color: #d81b60; border: 1.5px solid #ffd1e3;">
                <i class="bi bi-cart-x fs-2 me-2"></i> Your cart is empty.
              </div>
              @else
              <div class="table-responsive table-responsive-mobile">
                <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                  <thead class="table-light">
                    <tr>
                      <th class="fw-bold text-center" style="width: 110px;">Type</th>
                      <th class="fw-bold">Title / Info</th>
                      <th class="fw-bold text-center" style="width: 120px;">Added</th>
                      <th class="fw-bold text-center" style="width: 220px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php renderCartRows($cartItems); @endphp
                  </tbody>
                </table>
              </div>
              @endif
            </div>

            <div class="tab-pane fade" id="tab-catalogs" role="tabpanel">
              @if($catalogItems->isEmpty())
              <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                <i class="bi bi-cart-x fs-2 me-2"></i> No catalog items in your cart.
              </div>
              @else
              <div class="table-responsive table-responsive-mobile">
                <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                  <thead class="table-light">
                    <tr>
                      <th class="fw-bold text-center" style="width: 110px;">Type</th>
                      <th class="fw-bold">Title / Info</th>
                      <th class="fw-bold text-center" style="width: 120px;">Added</th>
                      <th class="fw-bold text-center" style="width: 220px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php renderCartRows($catalogItems); @endphp
                  </tbody>
                </table>
              </div>
              @endif
            </div>

            <div class="tab-pane fade" id="tab-alert" role="tabpanel">
              @if($alertItems->isEmpty())
              <div class="alert alert-info mb-0 rounded-3 shadow-sm text-center py-4 fs-5" style="background: #fff8f9; color: #d81b60; border: 1.5px solid #ffd1e3;">
                <i class="bi bi-cart-x fs-2 me-2"></i> No Alert Service items in your cart.
              </div>
              @else
              <div class="table-responsive table-responsive-mobile">
                <table class="table table-hover table-striped align-middle mb-0" style="border-radius: 1rem; overflow: hidden;">
                  <thead class="table-light">
                    <tr>
                      <th class="fw-bold text-center" style="width: 110px;">Type</th>
                      <th class="fw-bold">Title / Info</th>
                      <th class="fw-bold text-center" style="width: 120px;">Added</th>
                      <th class="fw-bold text-center" style="width: 220px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php renderCartRows($alertItems); @endphp
                  </tbody>
                </table>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('footer')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.cart-toggle').forEach(function(form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          var btn = form.querySelector('button');
          if (!btn) return;
          var original = btn.innerHTML;
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Removing';

          var formData = new FormData(form);
          fetch(form.action, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
            },
            body: formData
          }).then(function(res) {
            return res.json();
          }).then(function(data) {
            if (data && (data.status === 'removed' || data.status === 'added')) {
              var row = form.closest('tr');
              if (row) {
                row.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateY(-6px)';
                setTimeout(function() {
                  row.remove();
                }, 350);
              }
            } else {
              alert((data && data.message) || 'Unexpected response');
            }
          }).catch(function(err) {
            console.error(err);
            alert('Failed to remove item from cart.');
          }).finally(function() {
            btn.disabled = false;
            btn.innerHTML = original;
          });
        });
      });
    });
  </script>
</body>

</html>
