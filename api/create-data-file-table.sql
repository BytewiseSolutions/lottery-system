-- Create data_file table to store images in database
CREATE TABLE IF NOT EXISTS `data_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_data` longblob NOT NULL,
  `file_category` varchar(50) DEFAULT 'profile_picture',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_category` (`file_category`),
  CONSTRAINT `data_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add profile_picture column if it doesn't exist, or modify it if it does
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'user' 
                   AND COLUMN_NAME = 'profile_picture');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE user ADD COLUMN profile_picture INT(11) DEFAULT NULL AFTER country',
    'ALTER TABLE user MODIFY COLUMN profile_picture INT(11) DEFAULT NULL'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraint if it doesn't exist
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                  WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'user' 
                  AND CONSTRAINT_NAME = 'user_profile_picture_fk');

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE user ADD CONSTRAINT user_profile_picture_fk FOREIGN KEY (profile_picture) REFERENCES data_file(id) ON DELETE SET NULL',
    'SELECT "Foreign key already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
