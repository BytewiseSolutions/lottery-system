<?php
require_once 'config/database.php';

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

try {
    // Create database connection without specifying database
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    $db_name = $_ENV['DB_NAME'] ?? 'lottery_db';
    
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    echo "Database '$db_name' created.\n";
    
    // Use the database
    $pdo->exec("USE $db_name");
    $db = $pdo;
    
    echo "Starting database update...\n";
    
    // Drop foreign key constraints first
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop existing tables
    $db->exec("DROP TABLE IF EXISTS entries");
    $db->exec("DROP TABLE IF EXISTS winners");
    $db->exec("DROP TABLE IF EXISTS otp_verifications");
    $db->exec("DROP TABLE IF EXISTS users");
    
    echo "Dropped existing tables.\n";
    
    // Create updated users table
    $db->exec("
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
    )");
    
    echo "Created users table.\n";
    
    // Create OTP verification table
    $db->exec("
    CREATE TABLE otp_verifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        otp_code VARCHAR(6) NOT NULL,
        otp_type ENUM('email', 'phone') NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        is_used BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    echo "Created otp_verifications table.\n";
    
    // Recreate other tables
    $db->exec("
    CREATE TABLE entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lottery VARCHAR(50) NOT NULL,
        numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        draw_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    $db->exec("
    CREATE TABLE results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        winning_numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        draw_date DATE NOT NULL,
        total_pool_money DECIMAL(10,3) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("
    CREATE TABLE winners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lottery VARCHAR(50) NOT NULL,
        prize_amount DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "Created all tables successfully.\n";
    echo "Database update completed!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>