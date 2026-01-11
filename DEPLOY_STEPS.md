# STEP-BY-STEP DEPLOYMENT GUIDE

## Step 1: Upload the Fixed .htaccess
1. Upload `public_html.htaccess` to your Hostinger `public_html/` folder
2. Rename it to `.htaccess` (remove the "public_html" prefix)

## Step 2: Upload Web Files
Upload these files from `web/dist/web/browser/` to `public_html/`:
- index.html
- main-4557VBJF.js
- styles-N5Y4HVYV.css
- assets/ (entire folder)
- media/ (entire folder)
- favicon.ico

## Step 3: Upload API Files
Upload these files from `api/` to `public_html/api/`:
- All .php files
- config/ folder
- .env file
- .htaccess file

## Step 4: Set File Permissions
In Hostinger File Manager:
- Files: 644
- Folders: 755

## Step 5: Create/Update .env in public_html/api/
Create this file with your database credentials:

```
# Production Environment Configuration for Hostinger
APP_ENV=production

# Database Configuration
DB_HOST=localhost
DB_USER=u606331557_lottery_db
DB_PASSWORD=LotteryDb123
DB_NAME=u606331557_lottery_db

# JWT Secret
JWT_SECRET=a7f3e9c2b8d4f1a6e5c9b3d7f2a8e4c1b6d9f3a7e2c8b4d1f6a9e3c7b2d8f4a1

# CORS Settings
CORS_ORIGIN=https://totalfreelotto.com
```

## Step 6: Import Database
1. Go to Hostinger cPanel → phpMyAdmin
2. Select database: u606331557_lottery_db
3. Import your SQL file

## Step 7: Test Deployment
1. Visit: https://totalfreelotto.com
2. Test API: https://totalfreelotto.com/api/health
3. Test registration flow

## Troubleshooting
If still showing HTML:
1. Clear browser cache (Ctrl+F5)
2. Check browser console (F12)
3. Verify file permissions
4. Check .htaccess is uploaded correctly