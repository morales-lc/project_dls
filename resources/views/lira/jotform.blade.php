<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        .btn-pink {
            background: #e83e8c;
            color: #fff;
            border: none;
            font-weight: 600;
            transition: background 0.18s, color 0.18s;
        }

        .btn-pink:hover,
        .btn-pink:focus {
            background: #f06292;
            color: #fff;
        }

        .card {
            border-radius: 1.5rem !important;
        }

        .card-body {
            border-radius: 1.5rem !important;
        }
    </style>
</head>

<body>

    @include( 'navbar')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4" style="background: linear-gradient(135deg, #fff 80%, #ffe3f1 100%);">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-4 gap-3">
                            <div class="bg-pink d-flex align-items-center justify-content-center rounded-circle" style="width:56px;height:56px;">
                                <i class="bi bi-journal-bookmark-fill" style="font-size:2rem;color:#fff;"></i>
                            </div>
                            <div>
                                <h2 class="fw-bold mb-0" style="color:#e83e8c;font-size:2rem;">LiRA Request Form</h2>
                                <div class="text-muted" style="font-size:1.08rem;">Lourdes College Library</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <button id="fillFormBtn" class="btn btn-lg btn-pink shadow-sm px-4" style="background:#e83e8c;color:#fff;font-weight:600;border-radius:8px;border:none;">
                                <i class="bi bi-magic"></i> Fill Form with My Info
                            </button>
                        </div>
                        <div class="rounded-4 overflow-hidden border" style="box-shadow:0 2px 16px rgba(232,62,140,0.08);">
                            <iframe id="jotformFrame" src="https://form.jotform.com/221923899504465" width="100%" height="700" frameborder="0" style="border:0;min-height:700px;background:#fff;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')

    

    <script>
        document.getElementById('fillFormBtn').addEventListener('click', function() {
            var jotformUrl = "{{ $jotformUrl }}";
            document.getElementById('jotformFrame').src = jotformUrl;
        });
    </script>

</body>

</html>