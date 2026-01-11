# Deployment Instructions for Hostinger

## 1. Build the Angular Application
```bash
cd web
npm run build --prod
```

## 2. Upload Files to Hostinger

### Upload API files to public_html/api/
- Upload all files from `api/` folder to `public_html/api/`
- Make sure the `.env` file has the production configuration
- Set proper file permissions (644 for files, 755 for directories)

### Upload Web files to public_html/
- Upload all files from `web/dist/web/` to `public_html/`
- Upload the root `.htaccess` file to `public_html/`

## 3. Database Setup
- Import your database schema using phpMyAdmin or MySQL command line
- Update the database connection details in `api/.env`

## 4. File Structure on Hostinger
```
public_html/
├── api/
│   ├── config/
│   ├── .env (production config)
│   ├── .htaccess
│   └── *.php files
├── assets/
├── index.html
├── main.js
├── polyfills.js
├── runtime.js
├── styles.css
└── .htaccess (root)
```

## 5. Test the Deployment
- Visit https://totalfreelotto.com
- Test API endpoints: https://totalfreelotto.com/api/health
- Test registration and login functionality

## 6. SSL Certificate
- Ensure SSL certificate is active on Hostinger
- All API calls will use HTTPS

## 7. Environment Variables Check
Make sure these are set in `public_html/api/.env`:
- DB_HOST=localhost
- DB_USER=u606331557_lottery_db
- DB_PASSWORD=LotteryDb123
- DB_NAME=u606331557_lottery_db
- JWT_SECRET=(your secret)
- CORS_ORIGIN=https://totalfreelotto.com