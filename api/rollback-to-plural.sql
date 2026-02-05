-- Rollback script to rename tables from singular back to plural
-- Use this ONLY if you need to reverse the migration

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Rename user to users
ALTER TABLE user RENAME TO users;

-- Rename draw to draws
ALTER TABLE draw RENAME TO draws;

-- Rename entry to entries
ALTER TABLE entry RENAME TO entries;

-- Rename result to results
ALTER TABLE result RENAME TO results;

-- Rename winner to winners
ALTER TABLE winner RENAME TO winners;

-- Rename otp_code to otp_codes (if exists)
ALTER TABLE otp_code RENAME TO otp_codes;

-- Rename activity_log to activity_logs (if exists)
ALTER TABLE activity_log RENAME TO activity_logs;

-- Rename site_setting to site_settings (if exists)
ALTER TABLE site_setting RENAME TO site_settings;

-- Rename notification to notifications (if exists)
ALTER TABLE notification RENAME TO notifications;

-- Rename payment to payments (if exists)
ALTER TABLE payment RENAME TO payments;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify the rename was successful
SHOW TABLES;
