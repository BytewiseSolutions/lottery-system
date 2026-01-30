# Testing Winner Detection - Step by Step

## Step 1: Play the Lottery

1. Go to http://localhost:4200/lotteries
2. Login if not already logged in
3. Select "Monday Lotto" (Feb 2, 2026)
4. Choose these numbers:
   - Main Numbers: 5, 10, 15, 20, 25
   - Bonus Numbers: 30, 35
5. Submit the entry
6. Note: Remember these exact numbers!

## Step 2: Upload Results (Admin)

1. Go to http://localhost:4200/admin/results
2. Click "Upload New Result"
3. Fill in:
   - Lottery: Monday Lotto
   - Draw Date: Feb 2, 2026
   - Jackpot: $10.00 (or current amount)
   - Winning Numbers: 5, 10, 15, 20, 25
   - Bonus Numbers: 30, 35
   - Check "Publish Now"
4. Click Upload

## Step 3: Verify Winner

You should see:
- "1 Winners Found" message
- The result should show 1 winner

## Alternative: Use Test Script

Run this to create an entry and test:

```bash
cd /Users/lebohangmonamane/Documents/My\ Projects/lottery-system/api
php -r "
require_once 'config/database.php';
\$db = (new Database())->getConnection();
\$stmt = \$db->prepare('INSERT INTO entries (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (1, \"Monday Lotto\", ?, ?, \"2026-02-02\")');
\$stmt->execute(['[5,10,15,20,25]', '[30,35]']);
echo 'Test entry created with numbers [5,10,15,20,25] + [30,35]\n';
echo 'Now upload results with the same numbers via admin panel\n';
"
```

Then upload results via admin panel with matching numbers.
