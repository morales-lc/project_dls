# Login OTP Security Implementation

## Overview
This system adds an additional layer of security to username/password logins by requiring email verification through a One-Time Password (OTP). When users log in with their credentials, they must verify their identity by entering a 6-digit code sent to their registered email address.

## Features
- ✅ 6-digit OTP code generation
- ✅ Email delivery with professional template
- ✅ 5-minute expiration time
- ✅ Countdown timer on verification page
- ✅ Resend OTP functionality
- ✅ Session-based security
- ✅ Auto-focus and numeric input validation

## How It Works

### 1. Login Process
When a user logs in with username/email and password:
1. Credentials are validated
2. A 6-digit OTP is generated
3. OTP is stored in the database with 5-minute expiration
4. OTP is sent to user's email
5. User is redirected to OTP verification page

### 2. OTP Verification
On the verification page:
- User enters the 6-digit code received via email
- Countdown timer shows remaining time (5 minutes)
- If code is correct and not expired, user is logged in
- If code is incorrect, error message is shown
- User can request a new code using "Resend Code" button

### 3. Security Features
- OTP expires after 5 minutes
- Session-based authentication prevents unauthorized access
- OTP is cleared from database after successful verification
- Visual countdown timer alerts user of expiration
- Invalid attempts are logged with error messages

## Database Changes

### Migration: `add_login_otp_to_users_table`
Adds two columns to the `users` table:
```php
$table->string('login_otp', 6)->nullable();
$table->timestamp('login_otp_expires_at')->nullable();
```

## Files Modified/Created

### New Files
1. **`app/Mail/LoginOtpMail.php`**
   - Mailable class for sending OTP emails
   - Passes OTP code and user name to email template

2. **`resources/views/emails/login-otp.blade.php`**
   - Professional email template with branded design
   - Displays OTP code prominently
   - Includes security warnings

3. **`resources/views/auth/verify-otp.blade.php`**
   - OTP verification page with countdown timer
   - Auto-focus on input field
   - Numeric keyboard on mobile devices
   - Resend code functionality

### Modified Files
1. **`app/Models/User.php`**
   - Added `login_otp` and `login_otp_expires_at` to fillable
   - Added datetime cast for `login_otp_expires_at`

2. **`app/Http/Controllers/LoginController.php`**
   - Added OTP generation and email sending in `login()` method
   - Added `showOtpForm()` to display verification page
   - Added `verifyOtp()` to validate OTP and complete login
   - Added `resendOtp()` to send new OTP code

3. **`routes/web.php`**
   - Added route for OTP verification page: `GET /login/verify-otp`
   - Added route for OTP submission: `POST /login/verify-otp`
   - Added route for resending OTP: `POST /login/resend-otp`

## Routes

| Method | Route | Name | Description |
|--------|-------|------|-------------|
| POST | `/login` | `login` | Submit credentials and trigger OTP |
| GET | `/login/verify-otp` | `login.verify.otp` | Display OTP verification page |
| POST | `/login/verify-otp` | `login.verify.otp.submit` | Verify OTP code |
| POST | `/login/resend-otp` | `login.resend.otp` | Request new OTP code |

## Usage

### For Users
1. Navigate to login page
2. Click "Login with Username & Password"
3. Enter username/email and password
4. Check email for 6-digit verification code
5. Enter code on verification page
6. Successfully logged in!

### For Administrators
- No additional configuration needed
- OTP system works automatically for all username/password logins
- Google OAuth logins are NOT affected by this system

## Configuration

### Email Settings
Ensure your `.env` file has proper email configuration:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### OTP Expiration Time
To change the expiration time, modify the `login()` method in `LoginController.php`:
```php
$user->login_otp_expires_at = now()->addMinutes(5); // Change 5 to desired minutes
```

## Security Considerations

### Best Practices
✅ **Implemented:**
- OTP expires after 5 minutes
- OTP is cleared after successful verification
- Session-based verification prevents bypassing
- Email contains security warning
- Numeric-only input validation

### Additional Recommendations
- Consider rate limiting OTP requests
- Log failed OTP attempts for security monitoring
- Implement account lockout after multiple failed attempts
- Add IP address logging for suspicious activity

## Troubleshooting

### Email Not Received
1. Check spam/junk folder
2. Verify email configuration in `.env`
3. Check `storage/logs/laravel.log` for errors
4. Test email configuration: `php artisan tinker` then `Mail::raw('Test', fn($m) => $m->to('email@example.com')->subject('Test'));`

### OTP Expired Too Quickly
- Check server time is synchronized
- Verify timezone in `config/app.php`
- Increase expiration time if needed

### Session Issues
- Clear sessions: `php artisan session:clear`
- Check session driver in `.env`: `SESSION_DRIVER=file`

## Testing

### Manual Testing Steps
1. Login with valid credentials
2. Verify OTP email is received
3. Enter correct OTP → should login successfully
4. Enter incorrect OTP → should show error
5. Wait for expiration → should show expired error
6. Click "Resend Code" → should receive new email

### Test Accounts
Use existing user accounts in your database for testing.

## Future Enhancements
- [ ] SMS-based OTP as alternative to email
- [ ] Remember device option to skip OTP for trusted devices
- [ ] Rate limiting on OTP requests
- [ ] Admin dashboard for OTP monitoring
- [ ] Two-factor authentication (2FA) for enhanced security
- [ ] Backup codes for account recovery

## Support
For issues or questions, contact your system administrator.

---

**Last Updated:** November 18, 2025
**Version:** 1.0
