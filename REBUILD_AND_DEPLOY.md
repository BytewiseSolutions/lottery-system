# REBUILD AND DEPLOY FIX

## The Problem
Your main-4557VBJF.js file contains HTML entities (`&gt;`, `&lt;`, `&quot;`) instead of actual JavaScript code. This causes the browser to fail parsing it.

## Solution: Rebuild and Redeploy

### Step 1: Clean and Rebuild
```bash
cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/web
rm -rf dist/
npm run build
```

### Step 2: Verify Build Output
Check that `dist/web/browser/main-*.js` contains actual JavaScript (not HTML entities):
```bash
head -n 20 dist/web/browser/main-*.js
```

You should see JavaScript code like:
```javascript
var Xb=Object.defineProperty,eC=Object.defineProperties;
```

NOT HTML entities like:
```
var Xb=Object.defineProperty,eC=Object.defineProperties;var tC=Object.getOwnPropertyDescriptors;var Jh=Object.getOwnPropertySymbols;var nC=Object.prototype.hasOwnProperty,rC=Object.prototype.propertyIsEnumerable;var Xh=(e,n,t)=&gt;n in e?
```

### Step 3: Upload Correct .htaccess
Upload this to `public_html/.htaccess`:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Serve JavaScript files with correct MIME type
<FilesMatch "\.(js)$">
    Header set Content-Type "application/javascript; charset=utf-8"
    Header set X-Content-Type-Options "nosniff"
</FilesMatch>

# Serve CSS files with correct MIME type
<FilesMatch "\.(css)$">
    Header set Content-Type "text/css; charset=utf-8"
</FilesMatch>

# API routing
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^api/(.*)$ api/$1 [L]

# Angular routing - send all non-file requests to index.html
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api/
RewriteRule ^ index.html [L]

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/x-javascript "access plus 1 month"
</IfModule>
```

### Step 4: Upload Fresh Build
1. Delete ALL files in `public_html/` (except `api/` folder)
2. Upload fresh files from `dist/web/browser/`:
   - index.html
   - All .js files
   - All .css files
   - assets/ folder
   - media/ folder (if exists)
   - favicon.ico

### Step 5: Clear All Caches
1. Clear browser cache (Ctrl+Shift+Delete)
2. Clear Hostinger cache (if available in cPanel)
3. Test in incognito mode

### Step 6: Verify
Visit https://totalfreelotto.com and check browser console (F12).

You should see the site load correctly, NOT JavaScript errors about HTML entities.
