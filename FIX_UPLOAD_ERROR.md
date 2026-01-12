# FIX: Upload Result Error

## Problem
Frontend is calling old endpoint that requires `winners` field manually.

## Solution
Already fixed in code - just need to rebuild and redeploy.

## Steps to Fix:

### 1. Rebuild Frontend for Production
```bash
cd web
ng build --configuration production
```

### 2. Upload New Files to Hostinger
Upload these files from `web/dist/web/browser/` to `public_html/`:
- **main-*.js** (the new JavaScript file)
- index.html

### 3. Clear Browser Cache
- Press **Ctrl+Shift+R** (Windows) or **Cmd+Shift+R** (Mac)

## What Changed?
- Endpoint changed from `/api/admin-upload-result` → `/api/admin-upload-result-enhanced`
- New endpoint auto-calculates winners (no need to send `winners` field)
- Automatically matches entries and finds winners

## Test After Deploy
1. Go to Admin → Upload Results
2. Fill in the form
3. Click "Upload Result"
4. Should see success with winner count
