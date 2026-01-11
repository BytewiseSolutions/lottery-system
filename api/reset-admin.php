<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'config/database.php';
} catch (Exception $e) {
    die('Database config error: ' . $e->getMessage());
}

// Simple security - remove this line after running once
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL to execute');
}

header('Content-Type: text/plain');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die('Database connection failed');
    }
} catch (Exception $e) {
    die('Database connection error: ' . $e->getMessage());
}

try {
    // Find admin user by email or role
    $query = "SELECT * FROM users WHERE email = 'lebomona78@gmail.com' OR role = 'admin' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo "❌ Admin user not found! Creating new admin user...\n";
        
        // Create admin user
        $newPassword = 'Admin@2026!';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $insertQuery = "INSERT INTO users (full_name, email, password, role, email_verified, is_active) VALUES (?, ?, ?, 'admin', 1, 1)";
        $stmt = $db->prepare($insertQuery);
        $stmt->execute(['Administrator', 'lebomona78@gmail.com', $hashedPassword]);
        
        echo "✅ Admin user created successfully!\n";
        echo "Email: lebomona78@gmail.com\n";
        echo "Password: $newPassword\n";
        exit;
    }
    
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "📋 Admin User Details:\n";
    echo "ID: " . $admin['id'] . "\n";
    echo "Name: " . $admin['full_name'] . "\n";
    echo "Email: " . $admin['email'] . "\n";
    echo "Role: " . ($admin['role'] ?? 'user') . "\n";
    echo "Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "\n";
    echo "Email Verified: " . ($admin['email_verified'] ? 'Yes' : 'No') . "\n";
    echo "\n";
    
    // Reset password and ensure admin role
    $newPassword = 'Admin@2026!';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $updateQuery = "UPDATE users SET email = ?, password = ?, role = 'admin', is_active = 1, email_verified = 1 WHERE id = ?";
    $stmt = $db->prepare($updateQuery);
    $stmt->execute(['lebomona78@gmail.com', $hashedPassword, $admin['id']]);
    
    echo "✅ Admin updated successfully!\n";
    echo "Email: lebomona78@gmail.com\n";
    echo "Password: $newPassword\n";
    
} catch(PDOException $exception) {
    echo "❌ Error: " . $exception->getMessage() . "\n";
}
?>