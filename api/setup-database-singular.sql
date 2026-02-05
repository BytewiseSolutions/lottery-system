
-- Production database setup for Hostinger
-- The database u606331557_lottery_db already exists
-- Just import this file directly in phpMyAdmin

-- User table
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    full_name VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Upcoming draw table
CREATE TABLE IF NOT EXISTS upcoming_draw (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery VARCHAR(50) NOT NULL,
    draw_date DATETIME NOT NULL,
    jackpot DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lottery_date (lottery, draw_date)
);

-- Past draw table
CREATE TABLE IF NOT EXISTS past_draw (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery VARCHAR(50) NOT NULL,
    draw_date DATETIME NOT NULL,
    jackpot DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lottery_date (lottery, draw_date)
);

-- Entry table
CREATE TABLE IF NOT EXISTS entry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lottery VARCHAR(50) NOT NULL,
    draw_date DATETIME NOT NULL,
    numbers JSON NOT NULL,
    bonus_numbers JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_user_lottery (user_id, lottery),
    INDEX idx_draw_date (draw_date)
);

-- Result table
CREATE TABLE IF NOT EXISTS result (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery VARCHAR(50) NOT NULL,
    draw_date DATETIME NOT NULL,
    winning_numbers JSON NOT NULL,
    bonus_numbers JSON NOT NULL,
    jackpot DECIMAL(15,2) NOT NULL,
    winners INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lottery_date (lottery, draw_date),
    INDEX idx_status (status)
);

-- Winner table
CREATE TABLE IF NOT EXISTS winner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    result_id INT NOT NULL,
    entry_id INT NOT NULL,
    prize_amount DECIMAL(15,2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    payment_date DATETIME,
    paid_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (result_id) REFERENCES result(id) ON DELETE CASCADE,
    FOREIGN KEY (entry_id) REFERENCES entry(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_status (status)
);

-- OTP table
CREATE TABLE IF NOT EXISTS otp_code (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_user_code (user_id, code)
);

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);

-- Site settings table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- API keys table
CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    api_key VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_api_key (api_key),
    INDEX idx_user (user_id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    sent_by INT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (sent_by) REFERENCES user(id) ON DELETE SET NULL,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created (created_at)
);

-- Payment table
CREATE TABLE IF NOT EXISTS payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    winner_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    payment_details TEXT,
    approved_by INT,
    approved_at DATETIME,
    processed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (winner_id) REFERENCES winner(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES user(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_transaction (transaction_id)
);

-- Insert your admin user (password: Admin@123)
INSERT INTO user (full_name, email, password, role, email_verified) 
VALUES ('Free Lotto', 'lebomona78@gmail.com', '$2y$10$YourNewPasswordHashHere', 'admin', TRUE)
ON DUPLICATE KEY UPDATE email=email;

-- Note: Replace the password hash above with a bcrypt hash of your desired password
-- Or keep your existing hash: $2y$12$un0rlEjJVbeQyc2T8ob17uwPZAFy1IYTZ25t.XLIJ2p1Qb1p4KiF.

-- Insert sample draws
INSERT INTO upcoming_draw (lottery, draw_date, jackpot) VALUES
('Monday Lotto', DATE_ADD(NOW(), INTERVAL 2 DAY), 1000000.00),
('Wednesday Lotto', DATE_ADD(NOW(), INTERVAL 4 DAY), 1500000.00),
('Friday Lotto', DATE_ADD(NOW(), INTERVAL 6 DAY), 2000000.00)
ON DUPLICATE KEY UPDATE lottery=lottery;
