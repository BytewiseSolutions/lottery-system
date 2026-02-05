-- Final migration to align production database with code
-- Run this on your Hostinger database

SET FOREIGN_KEY_CHECKS = 0;

-- Rename singular to plural where code expects plural
ALTER TABLE activity_log RENAME TO activity_logs;
ALTER TABLE notification RENAME TO notifications;
ALTER TABLE site_setting RENAME TO site_settings;

-- Create missing api_keys table
CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    api_key VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_api_key (api_key),
    INDEX idx_user (user_id)
);

-- Fix winner table - add missing columns
ALTER TABLE winner 
ADD COLUMN IF NOT EXISTS result_id INT AFTER user_id,
ADD COLUMN IF NOT EXISTS entry_id INT AFTER result_id,
ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending' AFTER prize_amount,
ADD COLUMN IF NOT EXISTS status ENUM('pending', 'paid', 'failed') DEFAULT 'pending' AFTER payment_status,
ADD COLUMN IF NOT EXISTS payment_date DATETIME AFTER status,
ADD COLUMN IF NOT EXISTS paid_at DATETIME AFTER payment_date;

-- Add foreign keys to winner table
ALTER TABLE winner
ADD CONSTRAINT IF NOT EXISTS winner_result_fk FOREIGN KEY (result_id) REFERENCES result(id) ON DELETE CASCADE,
ADD CONSTRAINT IF NOT EXISTS winner_entry_fk FOREIGN KEY (entry_id) REFERENCES entry(id) ON DELETE CASCADE;

-- Add indexes to winner table
ALTER TABLE winner
ADD INDEX IF NOT EXISTS idx_payment_status (payment_status),
ADD INDEX IF NOT EXISTS idx_status (status);

-- Drop unused tables
DROP TABLE IF EXISTS draw;
DROP TABLE IF EXISTS rate_limits;
DROP TABLE IF EXISTS otp_verifications;

SET FOREIGN_KEY_CHECKS = 1;

SHOW TABLES;
