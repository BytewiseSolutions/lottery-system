# Guide: Update Lottery Names from Abbreviated to Full Day Names

## Problem
The lottery system is currently showing abbreviated names like "Mon Lotto", "Wed Lotto", and "Fri Lotto" instead of the full names "Monday Lotto", "Wednesday Lotto", and "Friday Lotto".

## Solution
You need to update the database records to use the full day names. The code is already configured to use full names, but the database still contains the old abbreviated names.

## Option 1: Run the Update Script on Hostinger (Recommended)

1. **Upload the update script** (if not already uploaded):
   - The file `api/update-lottery-names.php` should already be on your server

2. **Run the script via SSH** (if you have SSH access):
   ```bash
   cd /path/to/your/api/directory
   php update-lottery-names.php
   ```

3. **Or run via browser** (temporary method):
   - Temporarily rename `update-lottery-names.php` to something secure like `update-lottery-names-temp-12345.php`
   - Visit: `https://your-domain.com/api/update-lottery-names-temp-12345.php`
   - Delete the file after running it for security

## Option 2: Update Manually via phpMyAdmin

1. **Log in to your Hostinger control panel**
2. **Open phpMyAdmin**
3. **Select your database** (`u606331557_lottery_db`)
4. **Click on the SQL tab**
5. **Run the following SQL commands**:

```sql
-- Update entries table
UPDATE entries SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto';
UPDATE entries SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto';
UPDATE entries SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto';

-- Update results table
UPDATE results SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto';
UPDATE results SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto';
UPDATE results SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto';

-- Update winners table
UPDATE winners SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto';
UPDATE winners SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto';
UPDATE winners SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto';
```

6. **Click "Go" to execute the queries**

## Verification

After running the update:

1. **Clear your browser cache** or open an incognito window
2. **Visit your lottery website**
3. **Check that the lottery names now show**:
   - "Monday Lotto" instead of "Mon Lotto"
   - "Wednesday Lotto" instead of "Wed Lotto"
   - "Friday Lotto" instead of "Fri Lotto"

## Files Already Updated

The following files have already been configured to use full day names:
- ✅ `api/draws.php` - API endpoint that returns lottery draws
- ✅ `web/src/app/services/lottery.service.ts` - Frontend service with fallback data
- ✅ `api/update-lottery-names.php` - Database update script
- ✅ `api/update-lottery-names.sql` - SQL update script

## Note

The code is already correct and uses full day names. You only need to update the existing database records that were created with the old abbreviated names.
