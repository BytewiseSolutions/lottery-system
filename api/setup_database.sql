CREATE DATABASE IF NOT EXISTS lottery_system;
USE lottery_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lottery_type VARCHAR(50) NOT NULL,
    draw_date DATE NOT NULL,
    numbers JSON NOT NULL,
    entry_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery_type VARCHAR(50) NOT NULL,
    draw_date DATE NOT NULL,
    winning_numbers JSON NOT NULL,
    bonus_numbers JSON,
    pool_money DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    requests INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_limit (identifier, endpoint)
);

CREATE TABLE otp_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    type ENUM('email', 'phone') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE upcoming_draws (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery_type VARCHAR(50) NOT NULL,
    draw_date DATE NOT NULL,
    jackpot DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE past_draws (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery_type VARCHAR(50) NOT NULL,
    draw_date DATE NOT NULL,
    winning_numbers JSON NOT NULL,
    bonus_numbers JSON,
    pool_money DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE winners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lottery_type VARCHAR(50) NOT NULL,
    draw_date DATE NOT NULL,
    prize_amount DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO upcoming_draws (lottery_type, draw_date, jackpot) VALUES
('powerball', CURDATE() + INTERVAL 1 DAY, 50000000.00),
('mega-millions', CURDATE() + INTERVAL 2 DAY, 75000000.00),
('lotto', CURDATE() + INTERVAL 3 DAY, 25000000.00);

INSERT INTO past_draws (lottery_type, draw_date, winning_numbers, bonus_numbers, pool_money) VALUES
('powerball', CURDATE() - INTERVAL 1 DAY, '[7, 23, 45, 12, 68]', '[15]', 45000000.00),
('mega-millions', CURDATE() - INTERVAL 2 DAY, '[3, 17, 29, 41, 52]', '[8]', 65000000.00);