# Hostinger Deployment Checklist

## Files to Upload

### 1. Backend Files (Upload to `/api/` folder)
- [ ] All files from `/api/` directory
- [ ] Rename `.env.production` to `.env`
- [ ] Update database credentials in `.env`

### 2. Frontend Files (Upload to root directory)
- [ ] All files from `/web/dist/web/` directory
- [ ] Copy `.htaccess` to root directory

### 3. Database Setup
- [ ] Create MySQL database in Hostinger
- [ ] Import `init-db-updated.sql`
- [ ] Import `security-updates.sql`
- [ ] Test database connection

## Configuration Updates Needed

### .env file (in /api/ folder):
```
DB_HOST=localhost
DB_NAME=[your_hostinger_db_name]
DB_USER=[your_hostinger_db_user]  
DB_PASS=[your_hostinger_db_password]
JWT_SECRET=[generate_32_char_random_string]
APP_ENV=production
CORS_ORIGIN=https://[yourdomain.com]
```

### File Permissions:
```
Files: 644
Directories: 755
.env file: 600
```

## Testing After Deployment

- [ ] Visit homepage: https://yourdomain.com
- [ ] Test user registration
- [ ] Test user login
- [ ] Test lottery play functionality
- [ ] Test admin login: admin@totalfreelotto.com / Admin@2026!
- [ ] Test admin panel: https://yourdomain.com/admin

## Security Tasks (After Deployment)

- [ ] Change admin password
- [ ] Generate new JWT secret
- [ ] Enable HTTPS
- [ ] Test all API endpoints
- [ ] Verify CORS settings

## Quick Commands for Hostinger File Manager

1. Extract files to correct locations
2. Set file permissions
3. Test database connection
4. Verify .htaccess rules are working

Your lottery system is ready for deployment! 🚀