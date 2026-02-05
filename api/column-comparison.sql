-- Column comparison: Production vs Local

-- RESULT TABLE
-- Production columns:
-- id, lottery, winning_numbers, bonus_numbers, draw_date, jackpot, winners, status, notes, created_at
-- Local expected columns:
-- id, lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes, created_at

-- DIFFERENCES:
-- ✓ Same columns, just different order (no changes needed)

-- WINNER TABLE
-- Production columns:
-- id, user_id, lottery, prize_amount, created_at
-- Local expected columns:
-- id, user_id, result_id, entry_id, prize_amount, payment_status, status, payment_date, paid_at, created_at

-- DIFFERENCES:
-- MISSING in production: result_id, entry_id, payment_status, status, payment_date, paid_at
-- EXTRA in production: lottery (should be removed or kept for reference)

-- MIGRATION NEEDED FOR WINNER TABLE:
ALTER TABLE winner 
ADD COLUMN result_id INT AFTER user_id,
ADD COLUMN entry_id INT AFTER result_id,
ADD COLUMN payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending' AFTER prize_amount,
ADD COLUMN status ENUM('pending', 'paid', 'failed') DEFAULT 'pending' AFTER payment_status,
ADD COLUMN payment_date DATETIME AFTER status,
ADD COLUMN paid_at DATETIME AFTER payment_date;

-- Add foreign keys
ALTER TABLE winner
ADD CONSTRAINT winner_result_fk FOREIGN KEY (result_id) REFERENCES result(id) ON DELETE CASCADE,
ADD CONSTRAINT winner_entry_fk FOREIGN KEY (entry_id) REFERENCES entry(id) ON DELETE CASCADE;

-- Add indexes
ALTER TABLE winner
ADD INDEX idx_payment_status (payment_status),
ADD INDEX idx_status (status);
