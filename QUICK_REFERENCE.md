# Quick Reference - New Features

## 🚀 Quick Start

### 1. Run Database Migration
```bash
mysql -u user -p database < api/migration-add-notification-prefs.sql
```

### 2. Build Frontend
```bash
cd app && npm run build
```

### 3. Test Features
- Navigate to `/settings` for account management
- Navigate to `/past-draws` for history
- Check entries tab for filtering and limits

---

## 📍 API Endpoints

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/api/profile` | Get user profile | ✓ |
| PUT | `/api/profile` | Update profile | ✓ |
| POST | `/api/change-password` | Change password | ✓ |
| POST | `/api/notification-preferences` | Toggle notifications | ✓ |
| GET | `/api/entry-limit` | Get daily limit status | ✓ |
| GET | `/api/past-draws` | Get historical draws | ✗ |

---

## 🎯 Frontend Routes

| Route | Component | Purpose |
|-------|-----------|---------|
| `/settings` | SettingsPage | Account management |
| `/past-draws` | PastDrawsPage | Historical draws |

---

## 🔧 Key Services

### AuthService
```typescript
getProfile(): Observable<any>
updateProfile(data): Observable<any>
changePassword(current, new): Observable<any>
updateNotificationPreferences(enabled): Observable<any>
```

### LotteryService
```typescript
getPastDraws(): Observable<any>
getEntryLimit(): Observable<any>
```

### ShareService
```typescript
share(text, title): Promise<boolean>
```

---

## 📦 Components Modified

### EntriesComponent
- Added `filteredEntries` array
- Added `selectedLottery` filter
- Added `entryLimit` display
- Added `filterByLottery()` method

### ResultsComponent
- Added `shareResult()` method
- Added share button to template

### ProfileComponent
- Added Settings button
- Added Past Draws button

---

## 🎨 UI Elements

### Entry Limit Badge
```html
<div class="entry-limit-badge">
  <ion-icon name="ticket"></ion-icon>
  <span>{{ entryLimit.remaining }}/{{ entryLimit.limit }} entries remaining</span>
</div>
```

### Lottery Filter
```html
<ion-select [(ngModel)]="selectedLottery" (ionChange)="filterByLottery()">
  <ion-select-option value="all">All</ion-select-option>
  <ion-select-option *ngFor="let type of lotteryTypes" [value]="type">
    {{ type }}
  </ion-select-option>
</ion-select>
```

### Share Button
```html
<ion-button (click)="shareResult(result)">
  <ion-icon name="share-social" slot="start"></ion-icon>
  Share
</ion-button>
```

---

## 🗄️ Database Schema

### user table (updated)
```sql
notification_enabled TINYINT(1) DEFAULT 1
```

---

## 🔐 Security Notes

- All profile/settings endpoints require JWT authentication
- Password change validates current password
- Profile updates only allow name and phone changes
- Email changes not implemented (requires verification)

---

## 📱 Mobile Considerations

- Share uses native API on mobile devices
- Falls back to clipboard on web browsers
- All pages are responsive
- Touch-friendly UI elements

---

## 🐛 Common Issues

**Settings not loading?**
- Check JWT token is valid
- Verify `/api/profile` is accessible

**Past draws empty?**
- Ensure results exist in database
- Check draw dates are in the past

**Entry limit not updating?**
- Verify timezone settings
- Check entry creation timestamps

**Share not working?**
- Web browsers: Uses clipboard
- Mobile: Check share permissions

---

## 📊 Feature Status

✅ Settings/Account Management
✅ Past Draws History  
✅ Entry Filtering
✅ Daily Entry Limit Display
✅ Share Winning Numbers

---

## 🔗 Documentation Files

- `FEATURES_SUMMARY.md` - Complete feature overview
- `NEW_FEATURES.md` - Detailed implementation docs
- `SETUP_GUIDE.md` - Setup and troubleshooting
- `QUICK_REFERENCE.md` - This file

---

## 💡 Tips

1. Entry limit resets at midnight server time
2. Notification toggle affects future notifications only
3. Share text is formatted for readability
4. Filters are client-side (no API calls)
5. Past draws limited to 50 most recent

---

**Last Updated:** 2024
**Version:** 1.0
