# Setup Guide for New Features

## Backend Setup

### 1. Database Migration
Run the migration to add the notification preferences column:

```bash
# Connect to your database and run:
mysql -u your_user -p your_database < api/migration-add-notification-prefs.sql
```

Or manually execute:
```sql
ALTER TABLE user ADD COLUMN IF NOT EXISTS notification_enabled TINYINT(1) DEFAULT 1;
CREATE INDEX IF NOT EXISTS idx_notification_enabled ON user(notification_enabled);
```

### 2. API Files
The following new API files have been created and should be accessible:
- `api/profile.php`
- `api/change-password.php`
- `api/notification-preferences.php`
- `api/entry-limit.php`
- `api/terms.html`

Make sure your web server has read permissions for these files.

### 3. Update past-draw.php
The `api/past-draw.php` file has been updated to return results with winning numbers. No additional setup needed.

## Frontend Setup

### 1. Install Dependencies (if needed)
```bash
cd app
npm install
```

### 2. Build the App
```bash
# Development build
npm run build

# Production build
npm run build --prod
```

### 3. New Pages Created
- Settings page: `/settings`
- Past Draws page: `/past-draws`

Both are automatically added to the routing and accessible from the profile menu.

## Testing the Features

### 1. Settings/Account Management
1. Login to the app
2. Navigate to Profile → Settings
3. Test:
   - Update profile (name, phone)
   - Change password
   - Toggle notification preferences
   - Click Terms & Privacy links

### 2. Past Draws
1. Navigate to Profile → Past Draws
2. Test:
   - View historical draws
   - Filter by lottery type
   - Share winning numbers

### 3. Entry Filtering
1. Navigate to Entries tab
2. Test:
   - View entry limit badge (X/10 remaining)
   - Filter entries by lottery type
   - Submit new entry and watch limit update

### 4. Share Feature
1. Go to Results or Past Draws
2. Click "Share" button
3. Test:
   - Native share (on mobile)
   - Clipboard copy (fallback)

## Configuration

### Daily Entry Limit
The default limit is set to 10 entries per day. To change this:

Edit `api/entry-limit.php`:
```php
$dailyLimit = 10; // Change this value
```

### Notification Preferences
Users can toggle notifications on/off. The setting is stored in the `notification_enabled` column.

To check if notifications are enabled before sending:
```php
$stmt = $db->prepare("SELECT notification_enabled FROM user WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
if ($user['notification_enabled']) {
    // Send notification
}
```

## Troubleshooting

### Issue: Settings page not loading
- Check that `api/profile.php` is accessible
- Verify JWT authentication is working
- Check browser console for errors

### Issue: Past draws showing no data
- Verify `api/past-draw.php` is returning data
- Check that results exist in the database
- Ensure draw dates are in the past

### Issue: Entry limit not updating
- Check `api/entry-limit.php` is accessible
- Verify entries are being created with correct timestamps
- Check timezone settings in PHP

### Issue: Share not working
- On web: Will fallback to clipboard copy
- On mobile: Ensure app has share permissions
- Check browser console for errors

## Next Steps

1. Test all features thoroughly
2. Update any custom styling to match your brand
3. Add analytics tracking for new features
4. Consider adding email notifications for password changes
5. Add more legal pages (Privacy Policy, etc.)
