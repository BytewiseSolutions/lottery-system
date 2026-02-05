-- Migration script to rename tables from plural to singular
-- This preserves all data and relationships
-- Run this in phpMyAdmin on your Hostinger database

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Rename users to user
ALTER TABLE users RENAME TO user;

-- Rename draws to draw
ALTER TABLE draws RENAME TO draw;

-- Rename entries to entry
ALTER TABLE entries RENAME TO entry;

-- Rename results to result
ALTER TABLE results RENAME TO result;

-- Rename winners to winner
ALTER TABLE winners RENAME TO winner;

-- Rename otp_codes to otp_code (if exists)
ALTER TABLE otp_codes RENAME TO otp_code;

-- Rename activity_logs to activity_log (if exists)
ALTER TABLE activity_logs RENAME TO activity_log;

-- Rename site_settings to site_setting (if exists)
ALTER TABLE site_settings RENAME TO site_setting;

-- Rename notifications to notification (if exists)
ALTER TABLE notifications RENAME TO notification;

-- Rename payments to payment (if exists)
ALTER TABLE payments RENAME TO payment;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify the rename was successful
SHOW TABLES;
