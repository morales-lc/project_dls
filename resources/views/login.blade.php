<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <style>
        body { background: #fff; min-height: 100vh; }
        .login-title { font-size: 1.7rem; font-weight: 700; margin-top: 60px; text-align: center; }
        .login-box { max-width: 400px; margin: 40px auto 0 auto; border: 2px solid #6c63ff; border-radius: 10px; padding: 2.5rem 2rem 2rem 2rem; background: #fff; }
        .login-box label { font-weight: 500; }
        .login-box .form-control { background: #f8f9fa; }
        .login-box .form-check-label { font-size: 0.95rem; }
        .login-box .btn { background: #111; color: #fff; border-radius: 6px; font-weight: 600; }
        .login-box .btn:hover { background: #333; }
        .login-box .forgot-link { font-size: 0.95rem; }
        .login-box .form-text { font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="login-title">Login with your lccdo email account!</div>
    <div class="login-box shadow-sm">
        <h3 class="fw-bold text-center mb-4">Sign in</h3>
        <a href="{{ url('auth/google') }}">
    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google">
</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
