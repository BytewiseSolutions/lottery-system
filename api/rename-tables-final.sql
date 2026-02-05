-- Migration script to rename tables from plural to match your code
-- This preserves all data and relationships
-- Run this in phpMyAdmin on your Hostinger database

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Rename users to user (if exists)
ALTER TABLE IF EXISTS users RENAME TO user;

-- Rename upcoming_draws to upcoming_draw (if exists)
ALTER TABLE IF EXISTS upcoming_draws RENAME TO upcoming_draw;

-- Rename past_draws to past_draw (if exists)
ALTER TABLE IF EXISTS past_draws RENAME TO past_draw;

-- Rename entries to entry (if exists)
ALTER TABLE IF EXISTS entries RENAME TO entry;

-- Rename results to result (if exists)
ALTER TABLE IF EXISTS results RENAME TO result;

-- Rename winners to winner (if exists)
ALTER TABLE IF EXISTS winners RENAME TO winner;

-- Rename otp_codes to otp_code (if exists)
ALTER TABLE IF EXISTS otp_codes RENAME TO otp_code;

-- Keep activity_logs as-is (already correct)

-- Keep site_settings as-is (already correct)

-- Keep notifications as-is (already correct)

-- Keep api_keys as-is (already correct)

-- Rename payments to payment (if exists)
ALTER TABLE IF EXISTS payments RENAME TO payment;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify the rename was successful
SHOW TABLES;
