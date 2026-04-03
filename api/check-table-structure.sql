-- ============================================
-- CHECK EXISTING TABLE STRUCTURE
-- ============================================
-- Run these queries to see what columns already exist

-- Check vote table structure
DESCRIBE `vote`;

-- Check admin_vote table structure  
DESCRIBE `admin_vote`;

-- Check if specific columns exist
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'vote' 
AND TABLE_SCHEMA = DATABASE()
AND COLUMN_NAME IN ('draw_date', 'updated_at');

SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'admin_vote' 
AND TABLE_SCHEMA = DATABASE()
AND COLUMN_NAME IN ('draw_date', 'updated_at', 'voting_data', 'total_votes');

-- Check existing indexes
SHOW INDEX FROM `vote`;
SHOW INDEX FROM `admin_vote`;