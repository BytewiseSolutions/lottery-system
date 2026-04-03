
ALTER TABLE `vote` 
ADD COLUMN `draw_date` date DEFAULT NULL AFTER `vote_date`,
ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;

ALTER TABLE `admin_vote`
ADD COLUMN `voting_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`voting_data`)) AFTER `allocated_votes`,
ADD COLUMN `total_votes` int(11) DEFAULT 0 AFTER `voting_data`,
ADD COLUMN `draw_date` date DEFAULT NULL AFTER `vote_date`,
ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;

UPDATE `vote` SET `draw_date` = `vote_date` WHERE `draw_date` IS NULL;
UPDATE `admin_vote` SET `draw_date` = `vote_date` WHERE `draw_date` IS NULL;

CREATE INDEX `idx_vote_draw_date` ON `vote` (`draw_date`);
CREATE INDEX `idx_admin_vote_draw_date` ON `admin_vote` (`draw_date`);
CREATE INDEX `idx_vote_lottery_draw` ON `vote` (`lottery`, `draw_date`);
CREATE INDEX `idx_admin_vote_lottery_draw` ON `admin_vote` (`lottery`, `draw_date`);

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

COMMIT;