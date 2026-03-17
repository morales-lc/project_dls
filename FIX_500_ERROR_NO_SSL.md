# Fix 500 Error on Ubuntu Server Without SSL

## Problem
Your Laravel app on Ubuntu server (`/var/www/html/...`) is throwing 500 errors when creating accounts because:
1. Google OAuth callback URL doesn't match server domain
2. Session cookies may require HTTPS but server has no SSL
3. Configuration still points to localhost

## Immediate Fixes (Production Server)

### 1. Update `.env` on Ubuntu Server
SSH into your server and edit `/var/www/html/your-project/.env`:

```bash
sudo nano /var/www/html/project_dls/.env
```

**Change these values:**

```env
# Update APP_URL to your server's IP or domain
APP_URL=http://192.168.x.x  # Replace with actual server IP

# Force session cookies to work without SSL
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null

# Update Google OAuth redirect URL
GOOGLE_REDIRECT=http://192.168.x.x/auth/google/callback  # Replace with actual IP
```

### 2. Update Google OAuth Console
Go to [Google Cloud Console](https://console.cloud.google.com/):

1. Navigate to **APIs & Services > Credentials**
2. Click your OAuth 2.0 Client ID
3. Under **Authorized redirect URIs**, add:
   ```
   http://192.168.x.x/auth/google/callback
   http://your-server-ip/auth/google/callback
   ```
4. Save changes

### 3. Clear Laravel Cache
```bash
cd /var/www/html/project_dls
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 4. Set Proper Permissions
```bash
sudo chown -R www-data:www-data /var/www/html/project_dls/storage
sudo chown -R www-data:www-data /var/www/html/project_dls/bootstrap/cache
sudo chmod -R 775 /var/www/html/project_dls/storage
sudo chmod -R 775 /var/www/html/project_dls/bootstrap/cache
```

### 5. Check Laravel Logs
```bash
tail -f /var/www/html/project_dls/storage/logs/laravel.log
```

## Long-term Solution: Add SSL Certificate (FREE)

### Option A: Let's Encrypt (Recommended - FREE)

1. **Install Certbot:**
```bash
sudo apt update
sudo apt install certbot python3-certbot-apache  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx   # For Nginx
```

2. **Get SSL Certificate:**
```bash
# For Apache
sudo certbot --apache -d yourdomain.com

# For Nginx
sudo certbot --nginx -d yourdomain.com
```

3. **Update `.env` after SSL:**
```env
APP_URL=https://yourdomain.com
GOOGLE_REDIRECT=https://yourdomain.com/auth/google/callback
SESSION_SECURE_COOKIE=true
```

4. **Auto-renewal (certbot sets this up automatically):**
```bash
sudo certbot renew --dry-run
```

### Option B: Self-Signed Certificate (Testing Only)

```bash
# Generate certificate
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/selfsigned.key \
  -out /etc/ssl/certs/selfsigned.crt

# Configure Apache/Nginx to use it
# Note: Browsers will show security warning
```

## Debug Steps

### Check if sessions table exists:
```bash
mysql -u root -p
USE dls_project;
SHOW TABLES LIKE 'sessions';
SELECT * FROM sessions LIMIT 5;
```

If `sessions` table doesn't exist:
```bash
php artisan migrate
```

### Test Google OAuth:
1. Visit: `http://your-server-ip/auth/google`
2. Check Laravel logs for errors
3. Verify callback URL matches Google Console

### Common Issues:

**Error: "CSRF token mismatch"**
- Sessions not persisting due to secure cookie settings
- Fix: Set `SESSION_SECURE_COOKIE=false` in `.env`

**Error: "redirect_uri_mismatch"**
- Google OAuth callback doesn't match
- Fix: Update Google Console with correct server URL

**Error: "Permission denied" on storage**
- Wrong file permissions
- Fix: Run permission commands above

## Production Checklist

- [ ] Update `.env` with server IP/domain
- [ ] Set `SESSION_SECURE_COOKIE=false` (until SSL installed)
- [ ] Update Google OAuth redirect URLs
- [ ] Clear all Laravel caches
- [ ] Set proper storage permissions
- [ ] Test account creation
- [ ] Install SSL certificate (Let's Encrypt)
- [ ] Update `.env` back to secure settings after SSL

## Network Configuration

If accessing from Lourdes College network only:

1. **Static IP**: Ensure server has static IP on network
2. **Firewall**: Open port 80 (HTTP) and 443 (HTTPS)
   ```bash
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw reload
   ```

3. **DNS (Optional)**: Add local DNS entry for easier access
   - Example: `library.lccdo.local` → `192.168.x.x`

## Quick Test Command

```bash
# Test if app responds
curl -I http://your-server-ip

# Test Google OAuth (should redirect to Google)
curl -L http://your-server-ip/auth/google

# Check PHP errors
tail -f /var/log/apache2/error.log  # Apache
# OR
tail -f /var/log/nginx/error.log    # Nginx
```

## Security Notes

⚠️ **Without SSL:**
- Don't use in production long-term
- Passwords transmitted in plain text
- Google OAuth still secure (handled by Google)
- Only suitable for internal network testing

✅ **With SSL:**
- All data encrypted
- Secure cookies enabled
- Production-ready
- Required for external access
