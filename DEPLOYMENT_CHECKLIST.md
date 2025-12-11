# Deployment Checklist for Hostinger

## ✅ PRE-DEPLOYMENT CHECKLIST

### 1. API (Backend) - Ready ✓
- [x] Remove SendGrid/Twilio dependencies
- [x] Use native PHP mail() function
- [x] Database configuration via .env
- [x] .htaccess for URL rewriting
- [x] CORS configuration
- [x] JWT authentication
- [x] OTP verification system

### 2. Web (Frontend) - Needs Configuration
- [ ] Update production API URL
- [ ] Build for production
- [ ] Test production build locally

---

## 📋 DEPLOYMENT STEPS

### STEP 1: Prepare Production Environment File

**File: `/web/src/environments/environment.prod.ts`**

Update with your Hostinger domain:
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://totalfreelotto.com/api'
};
```

### STEP 2: Build Angular App

```bash
cd web
npm install
npm run build
```

This creates `/web/dist/lottery-web/browser/` folder with production files.

### STEP 3: Prepare API .env for Production

**File: `/api/.env`**

Update these values for Hostinger:
```
APP_ENV=production
DB_HOST=localhost
DB_USER=your_hostinger_db_user
DB_PASSWORD=your_hostinger_db_password
DB_NAME=your_hostinger_db_name
JWT_SECRET=a7f3e9c2b8d4f1a6e5c9b3d7f2a8e4c1b6d9f3a7e2c8b4d1f6a9e3c7b2d8f4a1
```

### STEP 4: Upload to Hostinger

#### A. Upload API (Backend)
1. Connect via FTP/File Manager
2. Upload entire `/api` folder to `public_html/api/`
3. Ensure `.htaccess` is uploaded
4. Set folder permissions: 755
5. Set `.env` file permissions: 644

#### B. Upload Web (Frontend)
1. Upload contents of `/web/dist/lottery-web/browser/` to `public_html/`
2. Files should be in root: `index.html`, `main-*.js`, etc.

**Final Structure:**
```
public_html/
├── index.html (Angular app)
├── main-*.js
├── styles-*.css
├── assets/
└── api/
    ├── .env
    ├── .htaccess
    ├── register.php
    ├── login.php
    └── config/
```

### STEP 5: Setup Database on Hostinger

1. **Create MySQL Database:**
   - Go to Hostinger Control Panel → Databases
   - Create new database
   - Create database user
   - Note: username, password, database name

2. **Import Database:**
   - Go to phpMyAdmin
   - Select your database
   - Import `/api/init-db-updated.sql`
   - Run `/api/add-email-verification.sql` if needed

3. **Update .env:**
   - Update DB credentials in `/api/.env`

### STEP 6: Configure Domain & SSL

1. **Point Domain:**
   - Ensure domain points to Hostinger
   - Wait for DNS propagation (up to 24 hours)

2. **Enable SSL:**
   - Hostinger Control Panel → SSL
   - Enable free SSL certificate
   - Force HTTPS redirect

3. **Update API URL:**
   - Update `environment.prod.ts` with HTTPS URL
   - Rebuild and re-upload if needed

### STEP 7: Test Production

Test these endpoints:
- ✓ `https://totalfreelotto.com` - Angular app loads
- ✓ `https://totalfreelotto.com/api/health` - Returns health status
- ✓ Register new user
- ✓ Verify email OTP
- ✓ Login
- ✓ Play lottery
- ✓ View results

---

## 🔧 HOSTINGER-SPECIFIC REQUIREMENTS

### Required:
- ✅ PHP 7.4+ (Hostinger provides)
- ✅ MySQL 5.7+ (Hostinger provides)
- ✅ Apache with mod_rewrite (Hostinger has)
- ✅ Email sending via mail() (Hostinger supports)

### Email Configuration:
- Hostinger's mail() function works automatically
- Emails sent from: `noreply@totalfreelotto.com`
- No additional configuration needed

---

## 🚨 IMPORTANT NOTES

### Security:
1. **Never commit .env to Git** - Already in .gitignore
2. **Change JWT_SECRET** in production
3. **Use strong database password**
4. **Enable HTTPS** (free with Hostinger)

### Email Delivery:
- In production (`APP_ENV=production`), emails sent via PHP mail()
- In development, OTP codes shown in API response
- Check spam folder if emails not received

### Database:
- Hostinger provides phpMyAdmin for database management
- Backup database regularly
- Database name format: `u123456789_lottery_db`

---

## 📝 QUICK DEPLOYMENT COMMANDS

```bash
# 1. Update production URL
# Edit: web/src/environments/environment.prod.ts

# 2. Build Angular app
cd web
npm run build

# 3. Files to upload:
# - web/dist/lottery-web/browser/* → public_html/
# - api/* → public_html/api/

# 4. Update .env on server with Hostinger DB credentials

# 5. Import database via phpMyAdmin
```

---

## ✅ POST-DEPLOYMENT CHECKLIST

- [ ] Website loads at your domain
- [ ] API health check works
- [ ] User registration works
- [ ] Email OTP received
- [ ] User login works
- [ ] Lottery games display
- [ ] Can play lottery
- [ ] Results page works
- [ ] SSL certificate active (HTTPS)
- [ ] No console errors in browser

---

## 🆘 TROUBLESHOOTING

### Issue: API returns 404
- Check .htaccess uploaded
- Verify mod_rewrite enabled (Hostinger has it by default)

### Issue: Database connection failed
- Verify .env credentials match Hostinger database
- Check database user has permissions

### Issue: Emails not sending
- Verify APP_ENV=production in .env
- Check spam folder
- Verify domain email is configured in Hostinger

### Issue: CORS errors
- Ensure API URL in environment.prod.ts matches actual domain
- Check CORS headers in config/cors.php

---

## 📞 SUPPORT

- Hostinger Support: https://www.hostinger.com/contact
- Check Hostinger Knowledge Base for PHP/MySQL issues
