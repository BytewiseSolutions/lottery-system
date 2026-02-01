<?php
require_once 'config/database.php';

$db = (new Database())->getConnection();

$email = 'lebomona78@gmail.com';
$password = 'Admin@2026!';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare("INSERT INTO users (full_name, email, password, role, email_verified) VALUES (?, ?, ?, 'admin', TRUE) ON DUPLICATE KEY UPDATE email=email");
    $stmt->execute(['Free Lotto', $email, $hashedPassword]);
    
    echo "Admin user created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
