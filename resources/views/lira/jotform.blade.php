<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiRA Request Form</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">

    <style>
        /* LiRA themed card to match ALINET blue border + light pink */
        .lira-form-card {
            background: #fff0f6;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            border: 4px solid #4a90e2;
            padding: 2.5rem 2rem 1.5rem 2rem;
        }

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

        .jotform-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .jotform-container iframe {
            width: 100%;
            height: 100%;
            min-height: 700px;
            border: 0;
        }

        /* Tablet view tweaks */
        @media (max-width: 768px) {
            .jotform-container iframe {
                min-height: 90vh;
            }

            .lira-form-card {
                padding: 1.5rem !important;
            }

            h2 {
                font-size: 1.5rem !important;
            }
        }

        /* Mobile view: remove container padding & make card full width */
        /* On small screens, remove Bootstrap padding and expand the form */
        @media (max-width: 576px) {

            /* Remove container padding and margins */
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            /* Expand column to 100% of screen */
            .col-lg-10,
            .col-lg-11,
            .col-xl-10 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Remove card side rounding so it blends to edges */
            .lira-form-card {
                border-radius: 0 !important;
                padding: 1rem !important;
            }

            /* Make iframe fully fill the card */
            .jotform-container iframe {
                width: 100% !important;
                min-height: 90vh !important;
                border: none !important;
            }
        }
    </style>
</head>

<body>
    @include('navbar')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="lira-form-card">
                        <div class="d-flex align-items-center mb-4 gap-3">
                            <div class="bg-pink d-flex align-items-center justify-content-center rounded-circle"
                                style="width:56px;height:56px;">
                                <i class="bi bi-journal-bookmark-fill" style="font-size:2rem;color:#fff;"></i>
                            </div>
                            <div>
                                <h2 class="fw-bold mb-0" style="color:#e83e8c;font-size:2rem;">LiRA Request Form</h2>
                                <div class="text-muted" style="font-size:1.08rem;">Lourdes College Library</div>
                            </div>
                        </div>



                    <div class="jotform-container rounded-4 overflow-hidden border"
                        style="box-shadow:0 2px 16px rgba(232,62,140,0.08); background:#fff;">
                        <iframe id="jotformFrame"
                            src="{{ $jotformUrl }}"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')


</body>

</html>