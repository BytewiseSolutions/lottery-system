# Deployment Checklist - Remaining Issues

## CRITICAL - Must Fix Before Deployment

### 1. Login Error Handling ❌
- **Issue**: No error message shown for invalid credentials
- **Status**: NEEDS FIX
- **File**: `/api/login.php` and `/web/src/app/login/`

### 2. Mobile Pop-up Visibility ⚠️
- **Issue**: Pop-up doesn't always appear on mobile
- **Status**: PARTIALLY FIXED (duration extended to 15s, but visibility issue remains)
- **File**: `/web/src/app/play-lottery/`

### 3. Invalid Lottery Dates ❌
- **Issue**: Some pages show "invalid" instead of dates
- **Status**: NEEDS INVESTIGATION
- **File**: `/web/src/app/lotteries/`

### 4. Lottery Closing Time ❌
- **Issue**: Must close at 7:00 PM on January 12, 2026
- **Status**: NEEDS IMPLEMENTATION
- **File**: `/api/play.php`

### 5. OTP Redirection Delays ❌
- **Issue**: 1-2 minute delays after OTP and login
- **Status**: NEEDS OPTIMIZATION
- **Files**: `/api/verify-otp.php`, `/api/login.php`

### 6. Show Password Text ❌
- **Issue**: Shows "Show Password" instead of proper toggle
- **Status**: NEEDS FIX
- **File**: `/web/src/app/register/`

### 7. Copyright Year ❌
- **Issue**: Shows 2020 instead of 2026
- **Status**: NEEDS UPDATE
- **File**: `/web/src/app/layout/footer/`

### 8. Lottery Results Display ❌
- **Issue**: Only 7 number slots instead of 8 (5 main + 2 bonus)
- **Status**: NEEDS FIX
- **File**: `/web/src/app/home/`

### 9. Section Headers Visibility ❌
- **Issue**: Headers not visible when landing on bonus numbers page
- **Status**: NEEDS FIX
- **File**: `/web/src/app/play-lottery/`

### 10. Submit/Edit Buttons Visibility ❌
- **Issue**: Sometimes need to scroll to see buttons
- **Status**: NEEDS FIX
- **File**: `/web/src/app/play-lottery/`

## COMPLETED ✅

1. Login Performance - 255ms with indexes
2. Timezone - Africa/Johannesburg (UTC+2)
3. Pop-up Duration - 15 seconds
4. Lottery Names - Monday/Wednesday/Friday Lotto
5. Jackpot Increment - $0.01 per entry
6. Logo Mapping - Correct logos for each lottery
7. Quick Pick - Random number selection
8. Admin Upload - Working with auth
9. Auto-archive - Cron job active
10. Currency Display - Dollar signs added
11. Edit Results - PUT endpoint working
12. Security Message - Can be updated in About page

## ADMIN ACCESS
- URL: http://localhost:4200/#/admin
- Email: admin@totalfreelotto.com
- Password: [Need to provide]

## SOCIAL MEDIA
- Recommendation: Facebook and Twitter only
- Instagram: Not required initially
