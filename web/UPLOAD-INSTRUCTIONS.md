# WEB FILES TO UPLOAD TO HOSTINGER

## FOR WEB (Admin Dashboard):

### UPLOAD ONLY THE BUILT FILES:
Upload contents of: `web/dist/web/browser/` folder

This includes:
- index.html
- *.js files
- *.css files
- assets/ folder
- All other compiled files

### DO NOT UPLOAD:
- node_modules/
- src/
- .angular/
- *.ts files
- *.json config files
- README.md

## STEPS:
1. Build the web app first: `cd web && npm run build`
2. Upload only `web/dist/web/browser/*` to your Hostinger public_html or subdirectory
3. Add .htaccess for Angular routing (if not already there)

## .htaccess for Angular (create in web root):
```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
```
