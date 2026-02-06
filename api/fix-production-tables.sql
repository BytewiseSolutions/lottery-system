-- Fix production database table structures
-- Run this on Hostinger database

-- Fix result table
ALTER TABLE result 
MODIFY COLUMN draw_date DATETIME NOT NULL,
MODIFY COLUMN jackpot DECIMAL(15,2) NOT NULL;

-- Note: winning_numbers and bonus_numbers are already longtext which works with JSON
-- MySQL will handle JSON encoding/decoding automatically
