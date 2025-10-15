<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <style>
        body { background-color: #fff; min-height: 100vh; }
        .login-title { font-size: 2rem; font-weight: 700; margin-top: 60px; text-align: center; color: #000000ff; }
        .login-box { max-width: 420px; margin: 40px auto 0 auto; border: none; border-radius: 16px; padding: 2.5rem 2rem 2rem 2rem; background: #fff; box-shadow: 0 8px 32px rgba(108,99,255,0.15); }
        .login-box label { font-weight: 500; }
        .login-box .form-control { background: #ffffffff; border-radius: 8px; }
        .login-box .form-check-label { font-size: 0.95rem; }
        .login-box .btn-primary { background: #6c63ff; color: #fff; border-radius: 8px; font-weight: 600; border: none; }
        .login-box .btn-primary:hover { background: #554eea; }
        .login-box .btn-google { display: block; margin: 0 auto 1rem auto; border-radius: 8px; }
        .login-box .forgot-link { font-size: 0.95rem; }
        .login-box .form-text { font-size: 0.95rem; }
        .divider { text-align: center; margin: 1.5rem 0; color: #aaa; font-size: 1rem; }
        .divider:before, .divider:after {
            content: '';
            display: inline-block;
            width: 40px;
            height: 1px;
            background: #ddd;
            vertical-align: middle;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column min-vh-100" style="background-color:#fff;">
        @include('navbar')
        <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center">
            <div class="login-title mb-2" style="color:#000; font-size:2.1rem; font-weight:700; margin-top:60px; text-shadow:0 2px 8px rgba(108,99,255,0.12);">Login with your LCCDO account</div>
            <div class="login-box shadow-lg" style="max-width:440px; width:100%; margin:40px auto 0 auto; border-radius:18px; padding:2.7rem 2.2rem 2.2rem 2.2rem; background:#fff; box-shadow:0 8px 32px rgba(108,99,255,0.18);">
                <h3 class="fw-bold text-center mb-4" style="color:#6c63ff;">Sign in</h3>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                @if(session('status'))
                    <div class="alert alert-info">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label" style="color:#6c63ff; font-weight:600;">Username or Email</label>
                        <input type="text" name="username" id="username" class="form-control" required autofocus value="{{ old('username') }}" style="background:#f8f9fa; border-radius:10px; border:1.5px solid #6c63ff;">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label d-flex justify-content-between align-items-center" style="color:#6c63ff; font-weight:600;">
                            <span>Password</span>
                            <a href="#" class="forgot-link text-decoration-none" style="color:#6c63ff;">Forgot password?</a>
                        </label>
                        <input type="password" name="password" id="password" class="form-control" required style="background:#f8f9fa; border-radius:10px; border:1.5px solid #6c63ff;">
                    </div>
                    <button type="submit" class="btn w-100 mb-2" style="background:#6c63ff; color:#fff; border-radius:10px; font-weight:600; border:none;">Login</button>
                </form>
                <div class="divider" style="text-align:center; margin:1.5rem 0; color:#6c63ff; font-size:1rem;">
                    <span style="display:inline-block; width:40px; height:1px; background:#b8b5ff; vertical-align:middle; margin:0 8px;"></span>
                    or
                    <span style="display:inline-block; width:40px; height:1px; background:#b8b5ff; vertical-align:middle; margin:0 8px;"></span>
                </div>
                <a href="{{ url('auth/google') }}">
                    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" class="btn-google" style="display:block; margin:0 auto 1rem auto; border-radius:10px;">
                </a>
            </div>
            <div style="height: 120px;"></div>
        </div>
        @include('footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
