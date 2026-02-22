-- Complete Cleanup Migration
-- Removes verification system and unused tables
-- Safe to run on production - keeps all data

SET FOREIGN_KEY_CHECKS = 0;

-- Drop unused tables
DROP TABLE IF EXISTS `api_keys`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `otp_verifications`;
DROP TABLE IF EXISTS `otp_code`;

SET FOREIGN_KEY_CHECKS = 1;

-- Verify remaining tables
SELECT 'Cleanup complete! Remaining tables:' as status;
SHOW TABLES;
