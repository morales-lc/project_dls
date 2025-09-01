<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #6c63ff 0%, #b8b5ff 100%); min-height: 100vh; }
        .login-title { font-size: 2rem; font-weight: 700; margin-top: 60px; text-align: center; color: #fff; }
        .login-box { max-width: 420px; margin: 40px auto 0 auto; border: none; border-radius: 16px; padding: 2.5rem 2rem 2rem 2rem; background: #fff; box-shadow: 0 8px 32px rgba(108,99,255,0.15); }
        .login-box label { font-weight: 500; }
        .login-box .form-control { background: #f8f9fa; border-radius: 8px; }
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
    <div class="login-title">Login with your LCCDO account</div>
    <div class="login-box shadow">
        <h3 class="fw-bold text-center mb-4" style="color:#6c63ff;">Sign in</h3>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label d-flex justify-content-between align-items-center">
                    <span>Password</span>
                    <a href="#" class="forgot-link text-decoration-none text-primary">Forgot password?</a>
                </label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
        </form>
        <div class="divider">or</div>
        <a href="{{ url('auth/google') }}">
            <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" class="btn-google">
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
