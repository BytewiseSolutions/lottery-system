# Hostinger Deployment Guide - totalfreelotto.com

## 🌐 Domain Information
- **Domain**: totalfreelotto.com
- **Hosting**: Hostinger
- **Admin URL**: https://totalfreelotto.com/#/admin
- **API URL**: https://totalfreelotto.com/api/

## 📁 File Structure on Hostinger

```
public_html/
├── api/                          # Backend PHP files
│   ├── config/
│   │   ├── database.php         # Update with Hostinger DB credentials
│   │   ├── cors.php
│   │   ├── jwt.php
│   │   └── ...
│   ├── login.php
│   ├── register.php
│   ├── play.php
│   ├── results.php
│   ├── auto-populate-draws.php
│   └── ...
├── index.html                    # Angular app entry point
├── assets/                       # Angular assets
├── *.js                         # Angular compiled files
├── *.css                        # Angular compiled files
└── .htaccess                    # URL rewriting rules
```

## 🔧 Step 1: Update Environment Configuration

### A. Update Angular Environment (Before Building)
**File**: `/web/src/environments/environment.prod.ts`

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://totalfreelotto.com/api'
};
```

### B. Build Angular App
```bash
cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/web
npm run build --configuration=production
```

This creates files in `web/dist/web/browser/`

## 🗄️ Step 2: Database Configuration

### A. Get Hostinger Database Credentials
1. Login to Hostinger hPanel
2. Go to **Databases** → **MySQL Databases**
3. Note down:
   - Database Name
   - Database User
   - Database Password
   - Database Host (usually localhost)

### B. Update .env file (Already configured!)
**Your .env file already has the correct Hostinger credentials:**

**File**: `/api/.env`

```env
# Database Configuration
DB_HOST=localhost
DB_USER=u606331557_lottery_db
DB_PASSWORD=LotteryDb123
DB_NAME=u606331557_lottery_db
```

✅ **No changes needed** - Just upload the existing `.env` file with your API folder!

### C. Import Database
1. Export your local database:
```bash
mysqldump -u root lottery_system > lottery_backup.sql
```

2. In Hostinger hPanel:
   - Go to **phpMyAdmin**
   - Select your database
   - Click **Import**
   - Upload `lottery_backup.sql`

## 📤 Step 3: Upload Files to Hostinger

### A. Upload via File Manager (Hostinger hPanel)
1. Go to **File Manager**
2. Navigate to `public_html/`
3. Upload:
   - **api/** folder → Upload entire folder
   - **Angular build files** → Upload contents of `web/dist/web/browser/` to `public_html/`

### B. Or Upload via FTP
```
Host: ftp.totalfreelotto.com
Username: [Your Hostinger FTP username]
Password: [Your Hostinger FTP password]
Port: 21
```

Upload structure:
```
public_html/
├── api/          (upload entire api folder)
├── index.html    (from Angular build)
├── assets/       (from Angular build)
└── *.js, *.css   (from Angular build)
```

## 🔐 Step 4: Set File Permissions

In Hostinger File Manager, set permissions:
- **Folders**: 755
- **PHP files**: 644
- **config/ folder**: 755
- **database.php**: 600 (more secure)

## 🌐 Step 5: Configure .htaccess

**File**: `public_html/.htaccess`

```apache
# Enable Rewrite Engine
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# API Routes - Don't rewrite API calls
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^ - [L]

# Angular Routes - Redirect all non-file requests to index.html
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]

# CORS Headers
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>

# PHP Settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value max_input_time 300
```

**File**: `public_html/api/.htaccess`

```apache
# API specific settings
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Security
<Files "config/database.php">
    Order allow,deny
    Deny from all
</Files>
```

## ⏰ Step 6: Set Up Cron Job

1. In Hostinger hPanel, go to **Advanced** → **Cron Jobs**
2. Add new cron job:

```
Frequency: Every hour (0 * * * *)
Command: /usr/bin/php /home/u123456789/public_html/api/auto-populate-draws.php
```

Or if that doesn't work:
```
Command: cd /home/u123456789/public_html/api && /usr/bin/php auto-populate-draws.php
```

## 🔑 Step 7: Set Admin Password

1. Upload and run `/api/reset-admin.php`:
```bash
# Access via browser:
https://totalfreelotto.com/api/reset-admin.php
```

2. Or via SSH (if available):
```bash
cd /home/u123456789/public_html/api
php reset-admin.php
```

3. **Delete reset-admin.php after use for security**

## 🧪 Step 8: Test Deployment

### Test URLs:
1. **Homepage**: https://totalfreelotto.com
2. **Admin Panel**: https://totalfreelotto.com/#/admin
3. **API Health**: https://totalfreelotto.com/api/health.php
4. **Upcoming Draws**: https://totalfreelotto.com/api/upcoming-draws.php

### Test Flows:
1. ✅ Register new user
2. ✅ Verify OTP
3. ✅ Login
4. ✅ Play lottery
5. ✅ View history
6. ✅ Admin login
7. ✅ Upload results

## 🔍 Step 9: Verify Cron Job

Check if cron is running:
```bash
# Via SSH or create a test file
# File: /api/cron-test.php
<?php
file_put_contents('/home/u123456789/cron-test.log', date('Y-m-d H:i:s') . " - Cron is working\n", FILE_APPEND);
?>
```

Add to cron:
```
*/5 * * * * /usr/bin/php /home/u123456789/public_html/api/cron-test.php
```

Check log after 5 minutes.

## 🛡️ Step 10: Security Checklist

- [ ] SSL Certificate enabled (HTTPS)
- [ ] Database credentials secured
- [ ] .env files not accessible
- [ ] reset-admin.php deleted
- [ ] File permissions set correctly
- [ ] CORS configured properly
- [ ] Rate limiting enabled
- [ ] Error logging enabled

## 📊 Step 11: Monitoring

### Check Logs:
1. **PHP Error Log**: Hostinger hPanel → Error Logs
2. **Cron Log**: Check cron job execution history in hPanel
3. **Database**: Monitor via phpMyAdmin

### Monitor:
- API response times
- Database size
- Cron job execution
- User registrations
- Entry submissions

## 🚨 Troubleshooting

### Issue: 500 Internal Server Error
- Check PHP error logs in hPanel
- Verify file permissions
- Check .htaccess syntax

### Issue: Database Connection Failed
- Verify credentials in database.php
- Check if database exists
- Ensure database user has permissions

### Issue: API Returns 404
- Check .htaccess in api/ folder
- Verify file paths
- Check mod_rewrite is enabled

### Issue: Cron Job Not Running
- Check cron command path
- Verify PHP path: `/usr/bin/php`
- Check file permissions
- View cron execution logs in hPanel

### Issue: CORS Errors
- Verify CORS headers in .htaccess
- Check api/config/cors.php
- Ensure Access-Control headers are set

## 📞 Admin Access

**Admin Panel**: https://totalfreelotto.com/#/admin
**Email**: admin@totalfreelotto.com
**Password**: [Set via reset-admin.php]

## 🎯 Post-Deployment Tasks

1. [ ] Test all user flows
2. [ ] Verify cron job runs hourly
3. [ ] Check jackpot increments
4. [ ] Test mobile experience
5. [ ] Verify email/SMS notifications
6. [ ] Monitor performance
7. [ ] Set up backups
8. [ ] Document any issues

## 💾 Backup Strategy

### Daily Backups (via Hostinger):
1. Go to **Backups** in hPanel
2. Enable automatic backups
3. Download manual backup weekly

### Database Backup (via Cron):
```bash
# Add to cron jobs
0 2 * * * mysqldump -u [user] -p[password] [database] > /home/u123456789/backups/db_$(date +\%Y\%m\%d).sql
```

## 📧 Support Contacts

- **Hostinger Support**: https://www.hostinger.com/support
- **Domain**: totalfreelotto.com
- **Hosting Panel**: https://hpanel.hostinger.com

---

**Deployment Date**: [To be filled]
**Deployed By**: [Your name]
**Version**: 1.0
**Status**: Ready for Production
