<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Login OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
    <style>
        body { background-color: #fff; min-height: 100vh; }
        .otp-title { font-size: 2rem; font-weight: 700; margin-top: 60px; text-align: center; color: #000000ff; }
        .otp-box { max-width: 480px; margin: 40px auto 0 auto; border: none; border-radius: 16px; padding: 2.5rem 2rem 2rem 2rem; background: #fff; box-shadow: 0 8px 32px rgba(108,99,255,0.15); }
        .otp-box label { font-weight: 500; }
        .otp-box .form-control { background: #f8f9fa; border-radius: 8px; font-size: 1.5rem; text-align: center; letter-spacing: 0.5rem; font-family: 'Courier New', monospace; border: 1.5px solid #6c63ff; }
        .otp-box .btn-primary { background: #6c63ff; color: #fff; border-radius: 8px; font-weight: 600; border: none; }
        .otp-box .btn-primary:hover { background: #554eea; }
        .otp-box .btn-outline { background: #fff; color: #6c63ff; border: 1.5px solid #6c63ff; border-radius: 8px; font-weight: 600; }
        .otp-box .btn-outline:hover { background: #f8f9fa; }
        .info-text { text-align: center; color: #666; font-size: 0.95rem; margin-bottom: 1.5rem; }
        .timer { text-align: center; color: #6c63ff; font-weight: 600; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="d-flex flex-column min-vh-100" style="background-color:#fff;">
        @include('navbar')
        <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center">
            <div class="otp-title mb-2" style="color:#000; font-size:2.1rem; font-weight:700; margin-top:60px; text-shadow:0 2px 8px rgba(108,99,255,0.12);">Verify Your Login</div>
            <div class="otp-box shadow-lg" style="max-width:500px; width:100%; margin:40px auto 0 auto; border-radius:18px; padding:2.7rem 2.2rem 2.2rem 2.2rem; background:#fff; box-shadow:0 8px 32px rgba(108,99,255,0.18);">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; color: #6c63ff;">🔐</div>
                </div>
                
                <h3 class="fw-bold text-center mb-3" style="color:#6c63ff;">Enter Verification Code</h3>
                
                <div class="info-text">
                    We've sent a 6-digit verification code to your email address. Please enter it below to complete your login.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <form method="POST" action="{{ route('login.verify.otp.submit') }}">
                    @csrf
                    <div class="mb-4">
                        <input type="text" 
                               name="otp" 
                               id="otp" 
                               class="form-control" 
                               required 
                               maxlength="6" 
                               pattern="[0-9]{6}"
                               placeholder="000000"
                               autocomplete="off"
                               inputmode="numeric"
                               style="background:#f8f9fa; border-radius:10px; border:1.5px solid #6c63ff; font-size:2rem; text-align:center; letter-spacing:0.8rem; font-family:'Courier New', monospace; padding:15px;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3" style="background:#6c63ff; color:#fff; border-radius:10px; font-weight:600; border:none; padding:12px;">
                        Verify & Login
                    </button>
                </form>

                <div class="timer mb-3" id="timer">
                    Code expires in: <span id="countdown">5:00</span>
                </div>

                <form method="POST" action="{{ route('login.resend.otp') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline w-100" style="background:#fff; color:#6c63ff; border:1.5px solid #6c63ff; border-radius:10px; font-weight:600;">
                        Resend Code
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" style="color:#6c63ff; text-decoration:none; font-size:0.95rem;">
                        ← Back to Login
                    </a>
                </div>
            </div>
            <div style="height: 120px;"></div>
        </div>
        @include('footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on OTP input
        document.getElementById('otp').focus();

        // Countdown timer (5 minutes)
        let timeLeft = 300; // 5 minutes in seconds
        const countdownEl = document.getElementById('countdown');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                countdownEl.textContent = 'Expired';
                countdownEl.style.color = '#dc3545';
                clearInterval(timerInterval);
            } else if (timeLeft <= 60) {
                countdownEl.style.color = '#dc3545';
            }
            
            timeLeft--;
        }
        
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        // Only allow numbers in OTP input
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
