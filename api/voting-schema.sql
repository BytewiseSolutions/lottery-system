-- Voting Feature Database Schema

CREATE TABLE `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`numbers`)),
  `bonus_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bonus_numbers`)),
  `vote_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_lottery_date` (`lottery`,`vote_date`),
  KEY `idx_user_date` (`user_id`,`vote_date`),
  CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `admin_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`numbers`)),
  `bonus_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bonus_numbers`)),
  `allocated_votes` int(11) DEFAULT 0,
  `vote_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_lottery_date` (`lottery`,`vote_date`),
  KEY `idx_admin` (`admin_id`),
  CONSTRAINT `admin_vote_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;
