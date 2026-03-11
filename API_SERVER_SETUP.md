# API Server Setup Guide

## Issue: 404 Errors on API Endpoints

The errors you're seeing mean the API server is not running or not accessible at `http://localhost:8000/api`

## Solution: Start the API Server

### Option 1: Using PHP Built-in Server (Recommended for Development)

```bash
# Navigate to API directory
cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/api

# Start PHP server on port 8000
php -S localhost:8000

# You should see:
# PHP 8.x Development Server (http://localhost:8000) started
```

### Option 2: Using XAMPP/MAMP

1. **Copy API folder to htdocs:**
   ```bash
   cp -r api /Applications/XAMPP/htdocs/lottery-api
   # or for MAMP:
   cp -r api /Applications/MAMP/htdocs/lottery-api
   ```

2. **Update environment.ts:**
   ```typescript
   export const environment = {
     production: false,
     apiUrl: 'http://localhost/lottery-api'  // Remove :8000
   };
   ```

3. **Start XAMPP/MAMP**

### Option 3: Using Apache/Nginx

1. **Configure virtual host to point to API directory**

2. **Update environment.ts with your domain**

## Verify API is Running

Open browser and test:
```
http://localhost:8000/health.php
```

Should return:
```json
{
  "status": "ok",
  "timestamp": "2024-03-11 17:00:00"
}
```

## Common Issues

### 1. Port 8000 Already in Use
```bash
# Find what's using port 8000
lsof -i :8000

# Kill the process or use different port
php -S localhost:8001

# Update environment.ts:
apiUrl: 'http://localhost:8001/api'
```

### 2. Permission Denied
```bash
# Give execute permissions
chmod +x api/*.php
```

### 3. Database Connection Failed
- Check `api/config/database.php`
- Verify MySQL is running
- Verify credentials are correct

## Testing Checklist

After starting API server:

1. ✅ Test health endpoint: `http://localhost:8000/health.php`
2. ✅ Test login: Try logging in from frontend
3. ✅ Test upload: Try uploading profile picture
4. ✅ Check browser console for errors

## Current Setup

Your frontend expects API at:
```
http://localhost:8000/api
```

So your API server must be running at:
```
http://localhost:8000
```

And files should be accessible at:
```
http://localhost:8000/upload-profile-picture.php
http://localhost:8000/get-file.php
http://localhost:8000/login.php
etc.
```

## Quick Start Command

```bash
# Terminal 1: Start API server
cd "/Users/lebohangmonamane/Documents/My Projects/lottery-system/api"
php -S localhost:8000

# Terminal 2: Start Angular app
cd "/Users/lebohangmonamane/Documents/My Projects/lottery-system/web"
npm start
```

Now both servers are running:
- Frontend: http://localhost:4200
- Backend: http://localhost:8000

## Fixed Issues

1. ✅ Created default-avatar.svg
2. ✅ Updated profile component to use SVG
3. ✅ Added .php extension to upload endpoint
4. ✅ Added better error handling
5. ✅ Added error messages

## Next Steps

1. Start API server using one of the options above
2. Refresh your browser
3. Try uploading profile picture again
4. Errors should be gone! 🎉
