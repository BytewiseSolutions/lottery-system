-- ============================================
-- MINIMAL DATABASE CHANGES FOR VOTING TIMING FIX
-- ============================================
-- These are the ESSENTIAL changes needed for the voting timing fix to work

-- Add draw_date column to vote table (separate from vote_date)
ALTER TABLE `vote` 
ADD COLUMN `draw_date` date DEFAULT NULL AFTER `vote_date`;

-- Add draw_date column to admin_vote table (separate from vote_date)
ALTER TABLE `admin_vote`
ADD COLUMN `draw_date` date DEFAULT NULL AFTER `vote_date`;

-- Update existing records to have draw_date same as vote_date
UPDATE `vote` SET `draw_date` = `vote_date` WHERE `draw_date` IS NULL;
UPDATE `admin_vote` SET `draw_date` = `vote_date` WHERE `draw_date` IS NULL;

-- Add performance indexes for the new columns
CREATE INDEX `idx_vote_draw_date` ON `vote` (`draw_date`);
CREATE INDEX `idx_admin_vote_draw_date` ON `admin_vote` (`draw_date`);
CREATE INDEX `idx_vote_lottery_draw` ON `vote` (`lottery`, `draw_date`);
CREATE INDEX `idx_admin_vote_lottery_draw` ON `admin_vote` (`lottery`, `draw_date`);

COMMIT;