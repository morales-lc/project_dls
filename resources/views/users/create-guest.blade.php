@extends('layouts.management')

@push('management-head')
<style>
    body { background: #f8f9fa; }
    .card { border: none; border-radius: 16px; background: #fff; }
    .form-label { font-weight: 600; color: #555; }
    .form-control { border-radius: 8px; padding: 10px; border: 1px solid #ccc; }
    .form-control:focus { border-color: #ffc107; box-shadow: 0 0 4px rgba(255, 193, 7, 0.4); }
    .btn-warning { background: #ffc107; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; }
    .btn-outline-secondary { border-radius: 8px; }
    h2 { color: #ffc107; }
</style>
<link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('title', 'Add Guest')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ request('return', route('user.management', ['type' => 'guest'])) }}" class="btn btn-outline-secondary px-4 py-2">&larr; Back to User Management</a>
        <span></span>
    </div>
    <h2 class="fw-bold mb-4">Add Guest</h2>
    <div class="card p-4 shadow rounded-4" style="max-width: 700px; margin:auto;">
    <form method="POST" action="{{ route('user.add') }}">
            @csrf
            <input type="hidden" name="role" value="guest">
            <input type="hidden" name="return_url" value="{{ request('return', url()->previous()) }}">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Account Status</label>
                    <select name="guest_account_status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Expiration Date (Optional)</label>
                    <input type="datetime-local" name="guest_expires_at" class="form-control" value="{{ old('guest_expires_at') }}">
                    <small class="text-muted">Leave empty for no expiration. Auto-set to 7 days for ALINET online approvals.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="guestPassword" class="form-control" required
                               >
                        <button class="btn btn-outline-secondary" type="button" id="togglePwBtn" tabindex="-1" aria-label="Show password">
                            <i class="bi bi-eye" id="togglePwIcon"></i>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" id="genPwBtn" tabindex="-1">Generate</button>
                    </div>
                    <div class="form-text">
                        Must be at least 6 characters with 1 uppercase, 1 lowercase, and 1 number.
                    </div>
                    <div class="alert alert-info mt-2 py-2 px-3" role="alert" style="font-size: 0.9rem;">
                        <strong>Note:</strong> Guest accounts created via ALINET online approval will automatically expire after 7 days. Manual guest accounts can have custom expiration dates.
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ request('return', route('user.management', ['type' => 'guest'])) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-warning">Add Guest</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('management-scripts')
<script>
    (function(){
        const pwInput = document.getElementById('guestPassword');
        const toggleBtn = document.getElementById('togglePwBtn');
        const toggleIcon = document.getElementById('togglePwIcon');
        const genBtn = document.getElementById('genPwBtn');

        if (toggleBtn && pwInput && toggleIcon) {
            toggleBtn.addEventListener('click', function(){
                const isPw = pwInput.type === 'password';
                pwInput.type = isPw ? 'text' : 'password';
                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
                toggleBtn.setAttribute('aria-label', isPw ? 'Hide password' : 'Show password');
            });
        }

        function generatePassword(length = 12) {
            const lowers = 'abcdefghijklmnopqrstuvwxyz';
            const uppers = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const digits = '0123456789';
            const all = lowers + uppers + digits;

            function pick(str) { return str[Math.floor(Math.random() * str.length)]; }

            let pwd = pick(lowers) + pick(uppers) + pick(digits);
            for (let i = 3; i < length; i++) {
                pwd += pick(all);
            }
            // Shuffle
            return pwd.split('').sort(() => Math.random() - 0.5).join('');
        }

        if (genBtn && pwInput) {
            genBtn.addEventListener('click', function(){
                const pwd = generatePassword(12);
                pwInput.value = pwd;
                // Reveal briefly so the admin can see/copied it
                pwInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
                toggleBtn.setAttribute('aria-label', 'Hide password');
                // Optional: select for quick copy
                pwInput.focus();
                pwInput.select();
            });
        }
    })();
    </script>
@endpush
