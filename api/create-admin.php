<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Admin credentials
$email = 'admin@totalfreelotto.com';
$password = 'Admin@2026!';
$fullName = 'System Administrator';

try {
    // Check if admin already exists
    $checkQuery = "SELECT id FROM users WHERE email = ? OR role = 'admin'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin user already exists!\n";
        exit;
    }
    
    // Create admin user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (full_name, email, password, role, is_active, email_verified_at) VALUES (?, ?, ?, 'admin', 1, NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute([$fullName, $email, $hashedPassword]);
    
    echo "✅ Admin user created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "\n⚠️  IMPORTANT: Change this password after first login!\n";
    
} catch(PDOException $exception) {
    echo "❌ Error creating admin user: " . $exception->getMessage() . "\n";
}
?>