-- Add country field to user table
ALTER TABLE `user` ADD COLUMN `country` VARCHAR(100) NULL AFTER `phone`;

-- Update existing users with a default value (optional)
-- UPDATE `user` SET `country` = 'South Africa' WHERE `country` IS NULL;
