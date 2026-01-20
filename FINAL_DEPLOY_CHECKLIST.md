# Final Pre-Deployment Checklist - totalfreelotto.com

## ✅ Your Hostinger Database Credentials

```
DB_HOST=localhost
DB_USER=u606331557_lottery_db
DB_PASSWORD=LotteryDb123
DB_NAME=u606331557_lottery_db
```

## 🚀 Deployment Steps (30 minutes)

### Step 1: Prepare Files Locally (10 min)

1. **Update Production Environment**
   ```bash
   cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/web/src/environments
   ```
   
   Edit `environment.prod.ts`:
   ```typescript
   export const environment = {
     production: true,
     apiUrl: 'https://totalfreelotto.com/api'
   };
   ```

2. **Build Angular App**
   ```bash
   cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/web
   npm run build --configuration=production
   ```
   
   Files will be in: `web/dist/web/browser/`

3. **Verify .env file (Already Done!)**
   ```bash
   cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/api
   cat .env
   ```
   
   Should show:
   ```
   DB_HOST=localhost
   DB_USER=u606331557_lottery_db
   DB_PASSWORD=LotteryDb123
   DB_NAME=u606331557_lottery_db
   ```
   
   ✅ **Already configured - no changes needed!**

4. **Export Database**
   ```bash
   cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system
   mysqldump -u root lottery_system > lottery_backup.sql
   ```

### Step 2: Upload to Hostinger (10 min)

1. **Login to Hostinger**
   - Go to: https://hpanel.hostinger.com
   - Login with your credentials

2. **Import Database**
   - Go to: **Databases** → **phpMyAdmin**
   - Select database: `u606331557_lottery_db`
   - Click **Import**
   - Upload `lottery_backup.sql`
   - Click **Go**

3. **Upload Files via File Manager**
   - Go to: **Files** → **File Manager**
   - Navigate to: `public_html/`
   
   **Upload API files:**
   - Upload entire `api/` folder
   - Make sure `.env` file is included
   
   **Upload Angular files:**
   - Upload all files from `web/dist/web/browser/` to `public_html/`:
     - index.html
     - assets/ folder
     - All .js files
     - All .css files
     - favicon.ico

4. **Create .htaccess**
   
   Create `public_html/.htaccess`:
   ```apache
   RewriteEngine On
   
   # Force HTTPS
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   
   # API Routes
   RewriteCond %{REQUEST_URI} ^/api/
   RewriteRule ^ - [L]
   
   # Angular Routes
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^ index.html [L]
   ```

### Step 3: Configure Cron Job (3 min)

1. In Hostinger hPanel, go to: **Advanced** → **Cron Jobs**

2. Add new cron job:
   ```
   Frequency: Every hour
   Minute: 0
   Hour: *
   Day: *
   Month: *
   Weekday: *
   
   Command: /usr/bin/php /home/u606331557/public_html/api/auto-populate-draws.php
   ```

### Step 4: Set Admin Password (2 min)

1. Visit: `https://totalfreelotto.com/api/reset-admin.php`
2. Set your admin password
3. **IMPORTANT**: Delete `reset-admin.php` after use

### Step 5: Test Everything (5 min)

Test these URLs:

1. ✅ **Homepage**: https://totalfreelotto.com
2. ✅ **API Health**: https://totalfreelotto.com/api/health.php
3. ✅ **Upcoming Draws**: https://totalfreelotto.com/api/upcoming-draws.php
4. ✅ **Admin Panel**: https://totalfreelotto.com/#/admin
   - Email: `admin@totalfreelotto.com`
   - Password: [Your set password]

Test user flows:
1. ✅ Register new account
2. ✅ Verify OTP
3. ✅ Login
4. ✅ Play lottery (Monday/Wednesday/Friday)
5. ✅ Check history
6. ✅ Admin: Upload results

## 🔐 Security Checklist

- [ ] SSL/HTTPS enabled (Hostinger auto-enables this)
- [ ] `.env` file uploaded with correct credentials
- [ ] `reset-admin.php` deleted after setting password
- [ ] File permissions correct (755 for folders, 644 for files)
- [ ] Database credentials not exposed in public files

## 📊 Post-Deployment Monitoring

### Check After 1 Hour:
- [ ] Cron job executed (check in Hostinger → Cron Jobs → Execution History)
- [ ] Upcoming draws auto-generated
- [ ] Past draws archived

### Check After 24 Hours:
- [ ] Users can register and login
- [ ] Lottery entries working
- [ ] Jackpot incrementing correctly ($0.01 per entry)
- [ ] Admin can upload results
- [ ] Mobile experience working

## 🆘 Troubleshooting

### If Database Connection Fails:
1. Check `.env` file exists in `/api/` folder
2. Verify credentials match:
   - DB_USER=u606331557_lottery_db
   - DB_PASSWORD=LotteryDb123
   - DB_NAME=u606331557_lottery_db
3. Check database exists in phpMyAdmin

### If API Returns 404:
1. Check `.htaccess` exists in `public_html/`
2. Verify API files uploaded to `public_html/api/`
3. Test direct file: `https://totalfreelotto.com/api/health.php`

### If Cron Job Not Running:
1. Check command path in Hostinger cron settings
2. Verify file exists: `/home/u606331557/public_html/api/auto-populate-draws.php`
3. Check execution logs in Hostinger

### If Admin Login Fails:
1. Run `reset-admin.php` again
2. Check database `users` table for admin user
3. Verify email: `admin@totalfreelotto.com`

## 📞 Important URLs

- **Website**: https://totalfreelotto.com
- **Admin Panel**: https://totalfreelotto.com/#/admin
- **Hostinger Panel**: https://hpanel.hostinger.com
- **phpMyAdmin**: Access via Hostinger → Databases

## 🎯 Admin Credentials

- **Email**: admin@totalfreelotto.com
- **Password**: [Set via reset-admin.php]
- **Panel**: https://totalfreelotto.com/#/admin

## ✅ Deployment Complete!

Once all steps are done and tests pass, your lottery system is LIVE! 🎉

Monitor for 24-48 hours and address any issues that arise.

---

**Domain**: totalfreelotto.com
**Database**: u606331557_lottery_db
**Status**: Ready to Deploy ✅
