CREATE DATABASE IF NOT EXISTS lottery_db;
USE lottery_db;

-- Drop existing users table if it exists
DROP TABLE IF EXISTS entries;
DROP TABLE IF EXISTS winners;
DROP TABLE IF EXISTS users;

-- Updated users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT check_email_or_phone CHECK (email IS NOT NULL OR phone IS NOT NULL)
);

-- OTP verification table
CREATE TABLE otp_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    otp_type ENUM('email', 'phone') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Recreate other tables
CREATE TABLE entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lottery VARCHAR(50) NOT NULL,
    numbers JSON NOT NULL,
    bonus_numbers JSON NOT NULL,
    draw_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery VARCHAR(50) NOT NULL,
    winning_numbers JSON NOT NULL,
    bonus_numbers JSON NOT NULL,
    draw_date DATE NOT NULL,
    total_pool_money DECIMAL(10,3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE winners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lottery VARCHAR(50) NOT NULL,
    prize_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);