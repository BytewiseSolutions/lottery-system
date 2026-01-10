# Hostinger Deployment Guide

## Prerequisites
1. Hostinger hosting account with PHP 8.0+ and MySQL
2. Domain name configured
3. File Manager or FTP access

## Step 1: Prepare Files

### Backend (API)
1. Copy entire `/api` folder to your hosting root directory
2. Rename `.env.production` to `.env`
3. Update `.env` with your Hostinger database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=your_hostinger_db_name
   DB_USER=your_hostinger_db_user
   DB_PASS=your_hostinger_db_password
   JWT_SECRET=generate_random_32_char_string
   APP_ENV=production
   CORS_ORIGIN=https://yourdomain.com
   ```

### Frontend (Web)
1. Build the Angular app for production:
   ```bash
   cd web
   npm run build
   ```
2. Upload contents of `web/dist/web/` to your hosting root directory

## Step 2: Database Setup

1. Create MySQL database in Hostinger control panel
2. Import the database schema:
   ```sql
   mysql -u username -p database_name < api/init-db-updated.sql
   ```
3. Create admin user:
   ```sql
   mysql -u username -p database_name < api/security-updates.sql
   ```

## Step 3: File Structure on Hostinger

```
public_html/
├── api/
│   ├── config/
│   ├── .env
│   ├── .htaccess
│   └── [all PHP files]
├── index.html
├── main.js
├── styles.css
└── [other Angular build files]
```

## Step 4: Configure .htaccess

Ensure this .htaccess is in your root directory:

```apache
RewriteEngine On

# Handle Angular routes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api/
RewriteRule . /index.html [L]

# API routes
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^api/(.*)$ /api/$1 [L]
```

## Step 5: Test Deployment

1. Visit your domain
2. Test login with admin credentials:
   - Email: admin@totalfreelotto.com
   - Password: Admin@2026!
3. Test user registration and lottery play

## Step 6: Security Checklist

- [ ] Change default admin password
- [ ] Update JWT secret key
- [ ] Enable HTTPS
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Remove debug files and logs

## Troubleshooting

### Common Issues:
1. **500 Error**: Check PHP error logs, verify database credentials
2. **CORS Issues**: Update CORS_ORIGIN in .env file
3. **API Not Found**: Verify .htaccess rules and file paths
4. **Database Connection**: Check Hostinger database settings

### File Permissions:
```bash
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 api/.env
```

## Admin Panel Access
- URL: https://yourdomain.com/admin
- Email: admin@totalfreelotto.com
- Password: Admin@2026! (change after first login)