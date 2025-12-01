# PHP Lottery API

PHP backend for the lottery system, compatible with Hostinger shared hosting.

## Setup Instructions

### 1. Upload Files to Hostinger
- Upload all files in `php-api/` folder to your domain's public_html directory
- Or upload to a subdirectory like `public_html/api/`

### 2. Configure Database
1. Create a MySQL database in Hostinger cPanel
2. Update `.env` file with your database credentials:
```
DB_HOST=localhost
DB_USER=your_db_username
DB_PASSWORD=your_db_password
DB_NAME=your_db_name
JWT_SECRET=your_random_secret_key
```

### 3. Initialize Database
Run the setup script by visiting: `https://yourdomain.com/setup-database.php`

### 4. Update Frontend
Update your Angular app's environment files to point to the new PHP API:
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://yourdomain.com/api'
};
```

## API Endpoints

All endpoints are accessible via:
- `GET /api/health` - Health check
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `GET /api/draws` - Get lottery draws
- `POST /api/play` - Submit lottery entry (requires auth)
- `GET /api/results` - Get lottery results
- `GET /api/entries` - Get all entries
- `GET /api/pool` - Get pool amounts
- `GET /api/stats` - Get system statistics

## Features
- ✅ User authentication with JWT
- ✅ Password hashing
- ✅ CORS support
- ✅ MySQL database integration
- ✅ URL rewriting for clean endpoints
- ✅ Compatible with shared hosting

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache with mod_rewrite enabled