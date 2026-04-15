CREATE TABLE IF NOT EXISTS `leading_numbers_snapshot` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lottery` VARCHAR(100) NOT NULL,
  `draw_date` DATE NOT NULL,
  `top_five` TEXT NOT NULL,
  `top_two` TEXT NOT NULL,
  `section1_data` LONGTEXT NOT NULL,
  `section2_data` LONGTEXT NOT NULL,
  `total_user_votes` INT NOT NULL DEFAULT 0,
  `total_admin_allocations` INT NOT NULL DEFAULT 0,
  `total_main_votes` INT NOT NULL DEFAULT 0,
  `total_bonus_votes` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_leading_numbers_snapshot` (`lottery`, `draw_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
