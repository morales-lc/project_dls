<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
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
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('status'))
                    <div class="alert alert-info">{{ session('status') }}</div>
                @endif
                
                <!-- Google Sign-In Button -->
                <a href="{{ url('auth/google') }}" class="btn w-100 d-flex align-items-center justify-content-center mb-3" style="background:#fff; color:#757575; border:1.5px solid #dadce0; border-radius:10px; font-weight:500; padding:12px; font-size:0.95rem;">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="margin-right:12px;">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        <path fill="none" d="M0 0h48v48H0z"/>
                    </svg>
                    Sign in using @lccdo.edu.ph google account
                </a>

                <div class="divider" style="text-align:center; margin:1.5rem 0; color:#6c63ff; font-size:1rem;">
                    <span style="display:inline-block; width:40px; height:1px; background:#b8b5ff; vertical-align:middle; margin:0 8px;"></span>
                    or
                    <span style="display:inline-block; width:40px; height:1px; background:#b8b5ff; vertical-align:middle; margin:0 8px;"></span>
                </div>

                <!-- Toggle Button for Username/Password Login -->
                <button type="button" id="toggleLoginForm" class="btn w-100 mb-3" style="background:#f8f9fa; color:#6c63ff; border:1.5px solid #6c63ff; border-radius:10px; font-weight:600;">
                    Login with Username & Password
                </button>

                <!-- Username/Password Form (Hidden by Default) -->
                <form method="POST" action="{{ route('login') }}" id="credentialsForm" style="display:none;">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label" style="color:#6c63ff; font-weight:600;">Username or Email</label>
                        <input type="text" name="username" id="username" class="form-control" required value="{{ old('username') }}" style="background:#f8f9fa; border-radius:10px; border:1.5px solid #6c63ff;">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label d-flex justify-content-between align-items-center" style="color:#6c63ff; font-weight:600;">
                            <span>Password</span>
                        
                        </label>
                        <input type="password" name="password" id="password" class="form-control" required style="background:#f8f9fa; border-radius:10px; border:1.5px solid #6c63ff;">
                    </div>
                    <button type="submit" class="btn w-100 mb-2" style="background:#6c63ff; color:#fff; border-radius:10px; font-weight:600; border:none;">Login</button>
                </form>
            </div>
            <div style="height: 120px;"></div>
        </div>
        @include('footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleLoginForm').addEventListener('click', function() {
            const form = document.getElementById('credentialsForm');
            const button = this;
            if (form.style.display === 'none') {
                form.style.display = 'block';
                button.textContent = 'Hide Username & Password Login';
                button.style.background = '#6c63ff';
                button.style.color = '#fff';
            } else {
                form.style.display = 'none';
                button.textContent = 'Login with Username & Password';
                button.style.background = '#f8f9fa';
                button.style.color = '#6c63ff';
            }
        });

        // If there are validation errors, automatically show the form
        @if ($errors->any())
            document.getElementById('credentialsForm').style.display = 'block';
            document.getElementById('toggleLoginForm').textContent = 'Hide Username & Password Login';
            document.getElementById('toggleLoginForm').style.background = '#6c63ff';
            document.getElementById('toggleLoginForm').style.color = '#fff';
        @endif
    </script>
</body>
</html>
