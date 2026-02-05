-- Column structure alignment script
-- Run this AFTER the table rename migration
-- Adds missing columns that your code expects

-- Add missing columns to user table
ALTER TABLE user 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER email_verified,
ADD COLUMN IF NOT EXISTS phone_verified BOOLEAN DEFAULT FALSE AFTER email_verified,
ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) AFTER password;

-- Update password_hash from password column if needed
UPDATE user SET password_hash = password WHERE password_hash IS NULL OR password_hash = '';

-- Verify changes
SELECT 'user table columns:' as info;
SHOW COLUMNS FROM user;
