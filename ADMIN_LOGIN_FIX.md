# Admin Login Fix - Deployment Guide

## Issues Fixed
1. ✅ Admin login now redirects to `/app/#/dashboard`
2. ✅ Dashboard requires authentication (can't access without login)
3. ✅ Auth guard protects admin routes
4. ✅ Uses real API authentication instead of hardcoded credentials

## Deployment Steps for Hostinger

### 1. Upload Updated Files

Upload these files to Hostinger via File Manager or FTP:

**API Files:**
- `api/config/cors.php`
- `api/login.php`
- `api/register.php`
- `api/play.php`
- `api/security-updates.sql`

**Web Files (rebuild and upload):**
```bash
cd web
ng build --configuration production
```
Then upload the contents of `web/dist/` to `public_html/app/`

### 2. Setup Database (via phpMyAdmin)

1. Go to cPanel → phpMyAdmin
2. Select `lottery_db`
3. Click **SQL** tab
4. Run this query to add admin user:

```sql
-- Add role column if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user';

-- Create rate_limits table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    endpoint VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_endpoint (ip_address, endpoint, created_at)
);

-- Insert admin user (replace the hash with your generated one)
INSERT INTO users (full_name, email, password, role, is_active, email_verified) 
VALUES ('Administrator', 'admin@totalfreelotto.com', '$2y$10$REPLACE_WITH_YOUR_HASH', 'admin', TRUE, TRUE)
ON DUPLICATE KEY UPDATE role = 'admin', password = '$2y$10$REPLACE_WITH_YOUR_HASH';
```

### 3. Generate Admin Password Hash

Create temporary file `public_html/api/hash.php`:
```php
<?php
echo password_hash('Admin@2026!', PASSWORD_DEFAULT);
?>
```

Visit: `https://totalfreelotto.com/api/hash.php`
Copy the hash and use it in the SQL above.
**Delete hash.php immediately after!**

### 4. Test Admin Login

1. Go to: `https://totalfreelotto.com/app/`
2. Click Login
3. Enter:
   - Email: `admin@totalfreelotto.com`
   - Password: `Admin@2026!`
4. Should redirect to: `https://totalfreelotto.com/app/#/dashboard`

### 5. Test Dashboard Protection

1. Try accessing: `https://totalfreelotto.com/app/#/dashboard` without login
2. Should redirect to home page
3. Login as admin
4. Now dashboard should be accessible

## Troubleshooting

### Login doesn't redirect to dashboard
- Check browser console for errors
- Verify API returns `role: 'admin'` in response
- Clear browser cache and localStorage

### Dashboard accessible without login
- Rebuild Angular app: `ng build --configuration production`
- Re-upload all files from `dist/` folder
- Clear browser cache

### API errors
- Check `api/config/cors.php` has correct domains
- Verify database credentials in `api/.env`
- Check PHP error logs in cPanel

## Security Checklist

- [ ] Admin password hash generated and updated in database
- [ ] Temporary hash.php file deleted
- [ ] CORS domains updated in cors.php
- [ ] .env file has APP_ENV=production
- [ ] JWT_SECRET is strong and unique
- [ ] Test login with wrong credentials (should fail)
- [ ] Test dashboard access without login (should redirect)
- [ ] Test rate limiting (try 6 failed logins)

## Admin Credentials

**Email:** admin@totalfreelotto.com  
**Password:** Admin@2026! (change this in production!)

## Support

If issues persist:
1. Check browser console (F12)
2. Check API response in Network tab
3. Verify database has admin user with role='admin'
4. Ensure all files uploaded correctly
