# New Features Implementation

## 1. Settings/Account Management

### Features Implemented:
- **Change Password**: Users can change their password from the settings page
- **Update Profile**: Users can update their name and phone number
- **Notification Preferences**: Toggle to enable/disable notifications
- **Terms & Privacy Links**: Direct links to terms and privacy policy pages

### Files Created/Modified:
- `/api/profile.php` - Get and update user profile
- `/api/change-password.php` - Change user password
- `/api/notification-preferences.php` - Update notification settings
- `/api/terms.html` - Terms and conditions page
- `/app/src/app/settings/` - Settings page component
- `/app/src/app/services/auth.service.ts` - Added profile management methods

### Usage:
Navigate to Settings from the profile menu to access all account management features.

## 2. Past Draws History

### Features Implemented:
- **Past Draws Page**: View historical lottery draws with results
- **Filter by Lottery Type**: Filter past draws by specific lottery
- **Share Winning Numbers**: Share results via native share or clipboard

### Files Created/Modified:
- `/app/src/app/past-draws/` - Past draws page component
- `/api/past-draw.php` - Updated to return results with winning numbers
- `/app/src/app/services/share.service.ts` - Share functionality service

### Usage:
Access past draws from the profile menu to view historical results and share them.

## 3. Entry Filtering

### Features Implemented:
- **Filter by Lottery Type**: Filter your entries by specific lottery
- **Dynamic Filter Options**: Only shows lotteries you have entries for

### Files Modified:
- `/app/src/app/components/entries/entries.component.ts` - Added filtering logic
- `/app/src/app/components/entries/entries.component.html` - Added filter UI
- `/app/src/app/components/entries/entries.component.scss` - Added filter styles

### Usage:
Use the dropdown filter on the entries page to view entries for specific lotteries.

## 4. Daily Entry Limit

### Features Implemented:
- **Entry Limit Display**: Shows remaining entries for the day (X/10)
- **Real-time Updates**: Updates after each entry submission
- **Visual Badge**: Prominent display at the top of entries page

### Files Created/Modified:
- `/api/entry-limit.php` - API endpoint to check daily limits
- `/app/src/app/services/lottery.service.ts` - Added getEntryLimit method
- `/app/src/app/components/entries/` - Display entry limit badge

### Usage:
The entry limit is automatically displayed on the entries page showing how many free entries remain for the day.

## 5. Share Winning Numbers

### Features Implemented:
- **Native Share API**: Uses browser/device native share when available
- **Fallback to Clipboard**: Copies to clipboard if share not available
- **Share from Multiple Places**: Available on results and past draws pages

### Files Created/Modified:
- `/app/src/app/services/share.service.ts` - Centralized share service
- `/app/src/app/components/results/` - Added share button to results
- `/app/src/app/past-draws/` - Added share button to past draws

### Usage:
Click the "Share" button on any result to share via your device's native share options or copy to clipboard.

## Database Changes

Run the following migration to add notification preferences:
```sql
ALTER TABLE user ADD COLUMN IF NOT EXISTS notification_enabled TINYINT(1) DEFAULT 1;
CREATE INDEX IF NOT EXISTS idx_notification_enabled ON user(notification_enabled);
```

## API Endpoints Added

1. `GET /api/profile` - Get user profile
2. `PUT /api/profile` - Update user profile
3. `POST /api/change-password` - Change password
4. `POST /api/notification-preferences` - Update notification settings
5. `GET /api/entry-limit` - Get daily entry limit status
6. `GET /api/past-draws` - Get past draws with results

## Navigation Updates

New routes added to the app:
- `/settings` - Settings and account management
- `/past-draws` - Historical draws and results

Both are accessible from the profile menu in the home page.
