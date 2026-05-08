SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE user (
 id INT AUTO_INCREMENT PRIMARY KEY,
 first_name VARCHAR(100) NOT NULL,
 last_name VARCHAR(100) NOT NULL,
 email VARCHAR(150) UNIQUE,
 phone VARCHAR(20) UNIQUE,
 country VARCHAR(100),
 password VARCHAR(255) NOT NULL,
 role ENUM('user','admin') DEFAULT 'user',
 is_active TINYINT(1) DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);
CREATE TABLE password_resets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE data_file (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 file_name VARCHAR(255) NOT NULL,
 file_type VARCHAR(50),
 file_size INT,
 file_path VARCHAR(255) NOT NULL,
 file_category VARCHAR(50) DEFAULT 'profile_picture',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE password_reset (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 reset_code VARCHAR(10) NOT NULL,
 expires_at DATETIME NOT NULL,
 used TINYINT(1) DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE activity_log (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 action VARCHAR(100) NOT NULL,
 details TEXT,
 ip_address VARCHAR(45),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL
);

CREATE TABLE lottery (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) UNIQUE NOT NULL,
 code VARCHAR(50) UNIQUE NOT NULL,
 main_numbers_count INT DEFAULT 5,
 bonus_numbers_count INT DEFAULT 2,
 jackpot DECIMAL(15,2) DEFAULT 10.00,
 is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE draw (
 id INT AUTO_INCREMENT PRIMARY KEY,
 lottery_id INT NOT NULL,
 draw_date DATETIME NOT NULL,
 status ENUM('scheduled','closed','completed','cancelled') DEFAULT 'scheduled',
 jackpot DECIMAL(15,2),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE(lottery_id, draw_date),
 FOREIGN KEY (lottery_id) REFERENCES lottery(id)
);

CREATE TABLE vote (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 lottery VARCHAR(50) NOT NULL,
 draw_id INT NOT NULL,
 numbers JSON NOT NULL,
 bonus_numbers JSON,
 source ENUM('user','admin') DEFAULT 'user',
 vote_date DATE NOT NULL,
 allocated_votes INT DEFAULT 1,
 total_votes INT DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL,
 FOREIGN KEY (draw_id) REFERENCES draw(id) ON DELETE CASCADE
);

CREATE TABLE admin_vote (
 id INT AUTO_INCREMENT PRIMARY KEY,
 admin_id INT NOT NULL,
 draw_id INT NULL,
 lottery VARCHAR(50) NOT NULL,
 numbers JSON NOT NULL,
 bonus_numbers JSON,
 allocated_votes INT DEFAULT 0,
 voting_data JSON NULL,
 total_votes INT DEFAULT 0,
 vote_date DATE NOT NULL,
 draw_date DATETIME NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_admin_vote_draw (draw_id),
 INDEX idx_admin_vote_lottery_date (lottery, draw_date),
 FOREIGN KEY (admin_id) REFERENCES user(id) ON DELETE CASCADE,
 FOREIGN KEY (draw_id) REFERENCES draw(id) ON DELETE SET NULL
);

CREATE TABLE entry (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 draw_id INT NOT NULL,
 lottery VARCHAR(50) NOT NULL,
 numbers JSON NOT NULL,
 bonus_numbers JSON,
 draw_date DATE NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
 FOREIGN KEY (draw_id) REFERENCES draw(id) ON DELETE CASCADE
);

CREATE TABLE result (
 id INT AUTO_INCREMENT PRIMARY KEY,
 draw_id INT NOT NULL UNIQUE,
 winning_numbers JSON NOT NULL,
 bonus_numbers JSON,
 jackpot DECIMAL(15,2),
 winners_count INT DEFAULT 0,
 status ENUM('published','draft') DEFAULT 'published',
 notes TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (draw_id) REFERENCES draw(id) ON DELETE CASCADE
);

CREATE TABLE winner (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 result_id INT NOT NULL,
 entry_id INT NULL,
 prize_amount DECIMAL(15,2) NOT NULL,
 claim_status ENUM('pending','claimed') DEFAULT 'pending',
 payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

 FOREIGN KEY (user_id) REFERENCES user(id),
 FOREIGN KEY (result_id) REFERENCES result(id),
 FOREIGN KEY (entry_id) REFERENCES entry(id)
);

CREATE TABLE payment (
 id INT AUTO_INCREMENT PRIMARY KEY,
 winner_id INT NOT NULL,
 user_id INT NOT NULL,
 amount DECIMAL(15,2) NOT NULL,
 payment_method VARCHAR(50),
 transaction_id VARCHAR(100),
 status ENUM('pending','processing','completed','failed') DEFAULT 'pending',
 approved_by INT NULL,
 approved_at DATETIME NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

 FOREIGN KEY (winner_id) REFERENCES winner(id),
 FOREIGN KEY (user_id) REFERENCES user(id),
 FOREIGN KEY (approved_by) REFERENCES user(id) ON DELETE SET NULL
);

CREATE TABLE notification (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 sent_by INT NULL,
 title VARCHAR(255),
 message TEXT,
 type ENUM('info','success','warning','error') DEFAULT 'info',
 is_read TINYINT(1) DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

 FOREIGN KEY (user_id) REFERENCES user(id),
 FOREIGN KEY (sent_by) REFERENCES user(id) ON DELETE SET NULL
);

CREATE TABLE contact_message (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100),
 email VARCHAR(150),
 message TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE system_setting (
 setting_key VARCHAR(100) PRIMARY KEY,
 setting_value TEXT NULL,
 updated_by INT NULL,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (updated_by) REFERENCES user(id) ON DELETE SET NULL
);

CREATE TABLE highest_vote (
 id INT AUTO_INCREMENT PRIMARY KEY,
 lottery VARCHAR(50) NOT NULL,
 draw_id INT NOT NULL,

 main_1 INT NOT NULL,
 main_2 INT NOT NULL,
 main_3 INT NOT NULL,
 main_4 INT NOT NULL,
 main_5 INT NOT NULL,

 bonus_1 INT NOT NULL,
 bonus_2 INT NOT NULL,

 total_main_votes INT DEFAULT 0,
 total_bonus_votes INT DEFAULT 0,

 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

 UNIQUE(lottery, draw_id),
 FOREIGN KEY (draw_id) REFERENCES draw(id) ON DELETE CASCADE
);
