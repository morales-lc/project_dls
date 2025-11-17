# Guest Account 7-Day Expiration Implementation

## Overview
This implementation ensures that guest accounts automatically expire 7 days after being approved through the ALINET system. After expiration, guests must submit a new ALINET request to regain access to the digital library system.

## What Was Implemented

### 1. Database Schema Changes
**File:** `database/migrations/2025_11_17_000000_add_guest_expiration_to_users_table.php`
- Added `guest_expires_at` (timestamp, nullable) - Tracks when guest account expires
- Added `guest_account_status` (enum: 'active'/'expired', default 'active') - Tracks account status

**Status:** ✅ Migration successfully applied

### 2. User Model Updates
**File:** `app/Models/User.php`
- Added `guest_expires_at` and `guest_account_status` to `$fillable` array
- Added `guest_expires_at` to `$casts` as datetime for automatic Carbon casting

### 3. Guest Account Auto-Creation
**File:** `app/Http/Controllers/AlinetAppointmentManageController.php`

When an **online** ALINET request is accepted:
1. Generates a random 10-character password
2. Creates a new User with:
   - `name`: From ALINET request
   - `email`: From ALINET request
   - `password`: Hashed random password
   - `role`: 'guest'
   - `guest_plain_password`: Encrypted password for email
   - `guest_expires_at`: 7 days from now
   - `guest_account_status`: 'active'
3. Passes the newly created guest user to the email Mailable

### 4. Email Template Updates
**Files:** 
- `app/Mail/AlinetAppointmentAccepted.php`
- `resources/views/mail/alinet/accepted.blade.php`

**Changes:**
- Mailable now accepts optional `$guestUser` parameter
- Passes `$expiresAt` to email template
- Email displays expiration date with 7-day warning for online mode guests

### 5. Login Validation
**File:** `app/Http/Controllers/LoginController.php`

**Checks performed before login:**
- If user is a guest with `guest_account_status === 'expired'` → reject login
- If user is a guest and `guest_expires_at` is in the past → mark as expired and reject login
- Displays message: "Your guest account has expired. Please submit a new ALINET request to get temporary access."

### 6. Real-Time Expiration Check (Middleware)
**File:** `app/Http/Middleware/CheckGuestExpiration.php`

**Applied to all web routes:**
- Checks on every request if authenticated user is a guest
- If guest account is expired OR expiration date has passed:
  - Marks account as expired (if not already)
  - Logs out the user
  - Invalidates session
  - Redirects to login with error message

**Registration:** `bootstrap/app.php`
- Registered as `guest.expiration` middleware alias
- Applied to all `web` middleware group routes

### 7. Automated Daily Cleanup
**File:** `app/Console/Commands/ExpireGuestAccounts.php`

**Command:** `php artisan guests:expire`

**Functionality:**
- Finds all guest accounts with `guest_account_status = 'active'`
- Where `guest_expires_at < now()`
- Updates their status to 'expired'
- Reports count of expired accounts

**Scheduling:** `routes/console.php`
- Scheduled to run daily automatically
- Requires cron job or task scheduler to run Laravel's scheduler

### 8. Guest Dashboard Warning
**File:** `resources/views/guest/dashboard.blade.php`

**Display Logic:**
- Calculates days remaining until expiration
- Shows warning banner if ≤ 7 days remaining
- Displays exact expiration date
- Encourages user to submit new ALINET request

## How It Works (User Flow)

### For External Users Requesting Online Access:

1. **User submits ALINET request** → Selects "Online" mode
2. **Admin reviews and approves** → Status changed to "accepted"
3. **System automatically:**
   - Creates guest User account
   - Sets expiration to 7 days from approval
   - Generates random password
   - Sends email with credentials and expiration date
4. **Guest logs in** → Can access digital library resources
5. **Guest uses system** → Warning banner shows days remaining
6. **After 7 days:**
   - Daily command marks account as expired
   - Guest cannot log in anymore
   - Error message directs them to request new ALINET access
7. **Guest requests new ALINET access** → Process repeats

### For Onsite Users:
- No guest account created
- Only appointment_date is set (next Saturday)
- Must visit physically on scheduled date

## Technical Details

### Expiration Timeline
- **Creation:** When online ALINET request is approved
- **Duration:** Exactly 7 days (168 hours)
- **Enforcement:** 
  - Real-time check on every request (middleware)
  - Login validation (prevents login)
  - Daily batch update (marks expired accounts)

### Security Considerations
- Passwords are hashed using Laravel's `Hash::make()`
- Plain passwords encrypted using `Crypt::encryptString()` for email only
- Expired accounts cannot be reactivated (must request new access)
- Session invalidation on expiration detection

### Database Queries
```sql
-- Check for expired accounts (daily command)
UPDATE users 
SET guest_account_status = 'expired' 
WHERE role = 'guest' 
  AND guest_account_status = 'active' 
  AND guest_expires_at < NOW();

-- Login validation
SELECT * FROM users 
WHERE email = ? 
  AND role = 'guest' 
  AND (guest_account_status = 'expired' OR guest_expires_at < NOW());
```

## Configuration

### Environment Requirements
- PHP 8.1+
- Laravel 11.x
- MySQL/MariaDB
- Task Scheduler (for automated expiration checks)

### Setting Up Task Scheduler

#### Linux/Ubuntu (Cron):
```bash
crontab -e
# Add this line:
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### Windows (Task Scheduler):
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: Daily at midnight
4. Action: Start a program
5. Program: `C:\path\to\php.exe`
6. Arguments: `artisan schedule:run`
7. Start in: `C:\path\to\project_dls`

## Testing Checklist

- [ ] Migration applies successfully (`php artisan migrate`)
- [ ] Guest account is created when online ALINET request is approved
- [ ] Email contains guest credentials and expiration date
- [ ] Guest can log in with provided credentials
- [ ] Guest dashboard shows expiration warning
- [ ] Guest cannot log in after expiration date
- [ ] Middleware logs out expired guests mid-session
- [ ] Command `php artisan guests:expire` marks expired accounts
- [ ] Guest can request new ALINET access after expiration

## Files Modified

1. ✅ `database/migrations/2025_11_17_000000_add_guest_expiration_to_users_table.php` - Created
2. ✅ `app/Models/User.php` - Updated fillable and casts
3. ✅ `app/Http/Controllers/AlinetAppointmentManageController.php` - Guest creation logic
4. ✅ `app/Mail/AlinetAppointmentAccepted.php` - Pass expiration date
5. ✅ `resources/views/mail/alinet/accepted.blade.php` - Display expiration
6. ✅ `app/Http/Controllers/LoginController.php` - Login validation
7. ✅ `app/Http/Middleware/CheckGuestExpiration.php` - Created
8. ✅ `bootstrap/app.php` - Middleware registration
9. ✅ `app/Console/Commands/ExpireGuestAccounts.php` - Created
10. ✅ `routes/console.php` - Command scheduling
11. ✅ `resources/views/guest/dashboard.blade.php` - Expiration warning

## Maintenance

### Manual Commands
```bash
# Mark expired guest accounts immediately
php artisan guests:expire

# Check scheduled tasks
php artisan schedule:list

# Test scheduler without waiting
php artisan schedule:run
```

### Monitoring
- Check `users` table for guest accounts with expired status
- Monitor email logs for ALINET acceptance emails
- Review application logs for middleware rejections

## Future Enhancements (Optional)

1. **Email reminders:** Send email 1-2 days before expiration
2. **Extension requests:** Allow guests to request 1-time extensions
3. **Usage analytics:** Track guest activity before expiration
4. **Graduated access:** Limit resources accessible in last 2 days
5. **Auto-archive:** Move expired guest data to archive table

## Support

For issues or questions about this implementation, contact the system administrator or refer to:
- Laravel Documentation: https://laravel.com/docs
- Project Documentation: `/md/LINUX_UBUNTU_DEPLOYMENT.txt`
