<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanning Service</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f9f9fb;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-text {
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            color: #e83e8c;
            margin: 40px auto 20px;
            letter-spacing: 1px;
            animation: fadeInDown 0.8s ease-in-out;
        }
        .divider {
            width: 1200px;
            height: 5px;
            background: #e83e8c;
            border-radius: 3px;
            margin: 0 auto 40px;
            animation: growWidth 1s ease-in-out;
        }
        .instructions {
            font-size: 1.1rem;
            line-height: 1.7;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .instructions:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        .instructions ol {
            margin-left: 1.5rem;
            padding-left: 0.5rem;
        }
        .instructions li {
            margin-bottom: 1rem;
        }
        .instructions a {
            color: #e83e8c;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .instructions a:hover {
            color: #c21765;
            text-decoration: underline;
        }
        .important {
            color: #e83e8c;
            font-weight: bold;
            margin-top: 2rem;
            font-size: 1.15rem;
        }
        .important-details {
            color: #333;
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }
        .extract-limits {
            font-size: 1rem;
            color: #333;
            margin-top: 1rem;
        }
        .extract-limits em {
            color: #666;
            font-size: 0.98rem;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes growWidth {
            from { width: 0; }
            to { width: 80px; }
        }
        @media (max-width: 768px) {
            .header-text { font-size: 1.8rem; padding: 0 1rem; }
            .instructions { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    @include('navbar')
    <div class="header-text">{{ $settings->title }}</div>
    <div class="container py-5">
        <div class="divider"></div>
        
        @if($settings->images && count($settings->images) > 0)
        <!-- Image Slideshow -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div id="scanningServiceCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($settings->images as $index => $image)
                        <button type="button" data-bs-target="#scanningServiceCarousel" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}" 
                            aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                            aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded shadow-sm" style="border: 2px solid #e83e8c;">
                        @foreach($settings->images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Scanning Service Image {{ $index + 1 }}" style="height: 450px; object-fit: cover;">
                        </div>
                        @endforeach
                    </div>
                    @if(count($settings->images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#scanningServiceCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#scanningServiceCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                                <div class="instructions">
                    <ol>
                        @if($settings->steps && count($settings->steps) > 0)
                            @foreach($settings->steps as $step)
                            <li>{!! $step !!}</li>
                            @endforeach
                        @else
                        <li>Log in using your <strong>@lccdo.edu.ph</strong> email.</li>
                        <li>Search the catalog you want to scan:
                            <a href="{{ route('dashboard') }}" target="_blank">Search Here</a>
                        </li>
                        <li>
                            Select your desired catalog/book and press <em>"Request Scan"</em> button.
                            This will redirect you to the LiRA Web Form with fields pre-filled.<br>
                        <li style="color:#e83e8c;">An email will be sent confirming the request as well as the Table of Contents so that you can choose the specific chapter/article needed</li>
                        <li style="color:#e83e8c;">When the digital file is available, an email notification will be sent with a link for downloading.<br>
                        <span style="color:#333;">The library will aim to provide you with a copy of the item in digital format within an average of three working days from the date it is requested.<br>
                        Exact time may vary based on the size of the file to be scanned.</span></li>
                        @endif
                    </ol>
                    @if($settings->important_note)
                    <div class="important">Important:</div>
                    <div class="important-details">
                        {!! $settings->important_note !!}
                    </div>
                    @endif
                    @if($settings->extract_limits)
                    <div class="extract-limits">
                        {!! $settings->extract_limits !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    @include('footer')
</body>
</html>
