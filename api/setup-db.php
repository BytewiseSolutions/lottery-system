<?php
/**
 * Complete Database Setup Script
 * Run this once to setup the entire database
 */

require_once 'config/database.php';

echo "=== Lottery System Database Setup ===\n\n";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die("❌ Database connection failed! Check your .env file.\n");
    }
    
    echo "✅ Database connected\n\n";
    
    // Create all tables
    echo "Creating tables...\n";
    
    $sql = "
    -- Users table
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE,
        phone VARCHAR(20) UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'user',
        email_verified BOOLEAN DEFAULT FALSE,
        phone_verified BOOLEAN DEFAULT FALSE,
        is_active BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT check_email_or_phone CHECK (email IS NOT NULL OR phone IS NOT NULL)
    );

    -- OTP verifications
    CREATE TABLE IF NOT EXISTS otp_verifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        otp_code VARCHAR(6) NOT NULL,
        otp_type ENUM('email', 'phone') NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        is_used BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Entries
    CREATE TABLE IF NOT EXISTS entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lottery VARCHAR(50) NOT NULL,
        numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        draw_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

    -- Results
    CREATE TABLE IF NOT EXISTS results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        winning_numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        draw_date DATE NOT NULL,
        jackpot VARCHAR(20) NOT NULL,
        winners INT DEFAULT 0,
        status VARCHAR(20) DEFAULT 'published',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Winners
    CREATE TABLE IF NOT EXISTS winners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lottery VARCHAR(50) NOT NULL,
        prize_amount DECIMAL(10,2) NOT NULL,
        result_id INT,
        entry_id INT,
        status VARCHAR(20) DEFAULT 'pending',
        draw_date DATE,
        paid_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
        FOREIGN KEY (entry_id) REFERENCES entries(id) ON DELETE CASCADE
    );

    -- Upcoming draws
    CREATE TABLE IF NOT EXISTS upcoming_draws (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        draw_date DATETIME NOT NULL,
        jackpot DECIMAL(10,2) DEFAULT 10.00,
        status VARCHAR(20) DEFAULT 'scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Past draws
    CREATE TABLE IF NOT EXISTS past_draws (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        draw_date DATE NOT NULL,
        winning_numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        jackpot DECIMAL(10,2) NOT NULL,
        winners INT DEFAULT 0,
        status VARCHAR(20) DEFAULT 'completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Rate limits
    CREATE TABLE IF NOT EXISTS rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        action_type VARCHAR(50) NOT NULL,
        endpoint VARCHAR(50) NOT NULL,
        attempts INT DEFAULT 1,
        last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip_action (ip_address, action_type)
    );
    ";
    
    $db->exec($sql);
    echo "✅ All tables created\n\n";
    
    // Create admin user
    echo "Creating admin user...\n";
    $adminEmail = 'admin@totalfreelotto.com';
    $adminPassword = password_hash('Admin@2026!', PASSWORD_BCRYPT);
    
    $stmt = $db->prepare("INSERT IGNORE INTO users (full_name, email, password, role, email_verified, is_active) VALUES (?, ?, ?, 'admin', TRUE, TRUE)");
    $stmt->execute(['Admin User', $adminEmail, $adminPassword]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Admin user created\n";
    } else {
        echo "ℹ️  Admin user already exists\n";
    }
    
    echo "\n=== Setup Complete ===\n";
    echo "Admin Login:\n";
    echo "  Email: admin@totalfreelotto.com\n";
    echo "  Password: Admin@2026!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
