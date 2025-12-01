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
    echo "Database '$db_name' created successfully.\n";
    
    // Use the database
    $pdo->exec("USE $db_name");
    
    // Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

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

    CREATE TABLE IF NOT EXISTS results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        winning_numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        draw_date DATE NOT NULL,
        total_pool_money DECIMAL(10,3) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS winners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lottery VARCHAR(50) NOT NULL,
        prize_amount DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );
    ";
    
    $pdo->exec($sql);
    echo "Tables created successfully.\n";
    echo "Database setup complete!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>