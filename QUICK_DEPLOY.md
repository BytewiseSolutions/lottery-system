# Quick Hostinger Deployment Checklist

## Pre-Deployment (Local)

- [ ] Update `web/src/environments/environment.prod.ts`:
  ```typescript
  apiUrl: 'https://totalfreelotto.com/api'
  ```

- [ ] Build Angular app:
  ```bash
  cd web
  npm run build --configuration=production
  ```

- [ ] Export database:
  ```bash
  mysqldump -u root lottery_system > lottery_backup.sql
  ```

## Hostinger Setup

### 1. Database (5 min)
- [ ] Login to Hostinger hPanel
- [ ] Go to **Databases** → Create MySQL database
- [ ] Note: DB name, user, password, host
- [ ] Go to **phpMyAdmin** → Import `lottery_backup.sql`

### 2. Verify Config (1 min)
- [ ] Check `api/.env` file has correct credentials:
  ```env
  DB_HOST=localhost
  DB_USER=u606331557_lottery_db
  DB_PASSWORD=LotteryDb123
  DB_NAME=u606331557_lottery_db
  ```
- [ ] ✅ Already configured - no changes needed!

### 3. Upload Files (10 min)
- [ ] Go to **File Manager** → `public_html/`
- [ ] Upload `api/` folder (entire folder)
- [ ] Upload Angular build files from `web/dist/web/browser/`:
  - index.html
  - assets/
  - *.js files
  - *.css files

### 4. Create .htaccess (3 min)
- [ ] Create `public_html/.htaccess`:
  ```apache
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  
  RewriteCond %{REQUEST_URI} ^/api/
  RewriteRule ^ - [L]
  
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^ index.html [L]
  ```

### 5. Set Permissions (2 min)
- [ ] Folders: 755
- [ ] PHP files: 644
- [ ] `config/database.php`: 600

### 6. Set Up Cron Job (3 min)
- [ ] Go to **Advanced** → **Cron Jobs**
- [ ] Add:
  ```
  Frequency: Every hour (0 * * * *)
  Command: /usr/bin/php /home/u123456789/public_html/api/auto-populate-draws.php
  ```

### 7. Set Admin Password (2 min)
- [ ] Visit: `https://totalfreelotto.com/api/reset-admin.php`
- [ ] Set password
- [ ] **DELETE reset-admin.php**

### 8. Test (5 min)
- [ ] Homepage: https://totalfreelotto.com
- [ ] Admin: https://totalfreelotto.com/#/admin
- [ ] API: https://totalfreelotto.com/api/health.php
- [ ] Register → OTP → Login → Play

## Post-Deployment

- [ ] Enable SSL (HTTPS) in Hostinger
- [ ] Set up automatic backups
- [ ] Monitor cron job execution
- [ ] Test on mobile devices
- [ ] Share admin credentials securely

## URLs

- **Website**: https://totalfreelotto.com
- **Admin Panel**: https://totalfreelotto.com/#/admin
- **Hostinger Panel**: https://hpanel.hostinger.com

## Admin Credentials

- **Email**: admin@totalfreelotto.com
- **Password**: [Set via reset-admin.php]

## Support

- **Hostinger Support**: https://www.hostinger.com/support
- **Documentation**: See HOSTINGER_DEPLOYMENT.md

---

**Total Time**: ~30 minutes
**Difficulty**: Easy
**Status**: Ready to Deploy ✅
