-- ============================================
-- CONDITIONAL DATABASE UPDATES FOR VOTING SYSTEM
-- ============================================
-- This script safely adds only the columns that don't already exist

-- Add updated_at to vote table if it doesn't exist
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `vote` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;',
        'SELECT "updated_at column already exists in vote table" as message;'
    )
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'vote' 
    AND TABLE_SCHEMA = DATABASE()
    AND COLUMN_NAME = 'updated_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add voting_data to admin_vote table if it doesn't exist
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `admin_vote` ADD COLUMN `voting_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`voting_data`)) AFTER `allocated_votes`;',
        'SELECT "voting_data column already exists in admin_vote table" as message;'
    )
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'admin_vote' 
    AND TABLE_SCHEMA = DATABASE()
    AND COLUMN_NAME = 'voting_data'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add total_votes to admin_vote table if it doesn't exist
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `admin_vote` ADD COLUMN `total_votes` int(11) DEFAULT 0 AFTER `voting_data`;',
        'SELECT "total_votes column already exists in admin_vote table" as message;'
    )
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'admin_vote' 
    AND TABLE_SCHEMA = DATABASE()
    AND COLUMN_NAME = 'total_votes'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add draw_date to admin_vote table if it doesn't exist
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `admin_vote` ADD COLUMN `draw_date` date DEFAULT NULL AFTER `vote_date`;',
        'SELECT "draw_date column already exists in admin_vote table" as message;'
    )
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'admin_vote' 
    AND TABLE_SCHEMA = DATABASE()
    AND COLUMN_NAME = 'draw_date'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add updated_at to admin_vote table if it doesn't exist
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `admin_vote` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;',
        'SELECT "updated_at column already exists in admin_vote table" as message;'
    )
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'admin_vote' 
    AND TABLE_SCHEMA = DATABASE()
    AND COLUMN_NAME = 'updated_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing records to have draw_date same as vote_date where draw_date is NULL
UPDATE `vote` SET `draw_date` = `vote_date` WHERE `draw_date` IS NULL;
UPDATE `admin_vote` SET `draw_date` = `vote_date` WHERE `draw_date` IS NULL;

-- Create indexes if they don't exist (these will fail silently if they already exist)
CREATE INDEX IF NOT EXISTS `idx_vote_draw_date` ON `vote` (`draw_date`);
CREATE INDEX IF NOT EXISTS `idx_admin_vote_draw_date` ON `admin_vote` (`draw_date`);
CREATE INDEX IF NOT EXISTS `idx_vote_lottery_draw` ON `vote` (`lottery`, `draw_date`);
CREATE INDEX IF NOT EXISTS `idx_admin_vote_lottery_draw` ON `admin_vote` (`lottery`, `draw_date`);

-- Create statistics views (will replace if they exist)
CREATE OR REPLACE VIEW `vote_statistics` AS
SELECT 
    lottery,
    draw_date,
    COUNT(*) as total_user_votes,
    COUNT(DISTINCT user_id) as unique_voters,
    JSON_ARRAYAGG(numbers) as all_numbers,
    JSON_ARRAYAGG(bonus_numbers) as all_bonus_numbers
FROM vote 
GROUP BY lottery, draw_date;

CREATE OR REPLACE VIEW `admin_vote_statistics` AS
SELECT 
    lottery,
    draw_date,
    SUM(allocated_votes) as total_allocated_votes,
    SUM(total_votes) as total_admin_votes,
    COUNT(*) as allocation_count
FROM admin_vote 
GROUP BY lottery, draw_date;

SELECT "Database update completed successfully!" as message;

COMMIT;