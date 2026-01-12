# ADMIN: UPLOAD LOTTERY RESULTS

## Method 1: Using Postman or API Tool (Recommended)

### Step 1: Open Postman
Download from: https://www.postman.com/downloads/

### Step 2: Create New Request
- Method: **POST**
- URL: `https://totalfreelotto.com/api/admin-upload-result-enhanced.php`

### Step 3: Set Headers
- Key: `Content-Type`
- Value: `application/json`

### Step 4: Add Body (JSON)
Select **Body** → **raw** → **JSON**, then paste:

```json
{
  "lottery": "Monday Lotto",
  "drawDate": "2026-01-20",
  "jackpot": 50000,
  "numbers": [5, 12, 23, 34, 45],
  "bonusNumbers": [7, 18],
  "publishNow": true,
  "notes": "Optional notes here"
}
```

### Step 5: Click Send
- Success: You'll see `"success": true` with winner count
- The system automatically finds winners from entries

---

## Method 2: Using cURL (Command Line)

```bash
curl -X POST https://totalfreelotto.com/api/admin-upload-result-enhanced.php \
  -H "Content-Type: application/json" \
  -d '{
    "lottery": "Wednesday Lotto",
    "drawDate": "2026-01-22",
    "jackpot": 75000,
    "numbers": [8, 15, 22, 31, 42],
    "bonusNumbers": [9, 16],
    "publishNow": true
  }'
```

---

## Field Reference

| Field | Type | Required | Example | Notes |
|-------|------|----------|---------|-------|
| lottery | string | Yes | "Monday Lotto" | Must be: "Monday Lotto", "Wednesday Lotto", or "Friday Lotto" |
| drawDate | string | Yes | "2026-01-20" | Format: YYYY-MM-DD |
| jackpot | number | Yes | 50000 | Amount in dollars (no $ sign) |
| numbers | array | Yes | [5, 12, 23, 34, 45] | Exactly 5 numbers |
| bonusNumbers | array | Yes | [7, 18] | Exactly 2 numbers |
| publishNow | boolean | No | true | true = published, false = draft |
| notes | string | No | "Special draw" | Optional notes |

---

## What Happens After Upload?

1. ✅ Result is saved to database
2. ✅ System automatically checks all entries for that draw
3. ✅ Winners are identified and recorded
4. ✅ Winner count is calculated
5. ✅ Result appears on website immediately (if publishNow = true)

---

## Prize Levels (Auto-Calculated)

- **Jackpot**: 5 numbers + 2 bonus
- **Second**: 5 numbers + 1 bonus
- **Third**: 5 numbers
- **Fourth**: 4 numbers
- **Fifth**: 3 numbers

---

## Quick Examples

### Monday Draw
```json
{
  "lottery": "Monday Lotto",
  "drawDate": "2026-01-20",
  "jackpot": 50000,
  "numbers": [5, 12, 23, 34, 45],
  "bonusNumbers": [7, 18],
  "publishNow": true
}
```

### Wednesday Draw
```json
{
  "lottery": "Wednesday Lotto",
  "drawDate": "2026-01-22",
  "jackpot": 75000,
  "numbers": [8, 15, 22, 31, 42],
  "bonusNumbers": [9, 16],
  "publishNow": true
}
```

### Friday Draw
```json
{
  "lottery": "Friday Lotto",
  "drawDate": "2026-01-24",
  "jackpot": 100000,
  "numbers": [3, 14, 27, 38, 49],
  "bonusNumbers": [11, 25],
  "publishNow": true
}
```

---

## Troubleshooting

**Error: "Missing required field"**
- Check all required fields are present

**Error: "Must provide exactly 5 winning numbers"**
- Ensure numbers array has exactly 5 values

**Error: "Must provide exactly 2 bonus numbers"**
- Ensure bonusNumbers array has exactly 2 values

**No winners found but entries exist**
- Check lottery name matches exactly
- Check drawDate matches entry dates
- Verify numbers are correct
