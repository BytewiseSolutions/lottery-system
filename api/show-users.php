<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

if (!isset($_GET['show'])) {
    die('Add ?show=1 to URL to execute');
}

header('Content-Type: text/plain');

$database = new Database();
$db = $database->getConnection();

try {
    // Show table structure
    echo "=== USERS TABLE STRUCTURE ===\n";
    $query = "DESCRIBE users";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo $column['Field'] . " | " . $column['Type'] . " | " . $column['Null'] . " | " . $column['Key'] . " | " . $column['Default'] . "\n";
    }
    
    echo "\n=== ALL USERS ===\n";
    $query = "SELECT * FROM users";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . "\n";
        echo "Name: " . ($user['full_name'] ?? 'N/A') . "\n";
        echo "Email: " . ($user['email'] ?? 'N/A') . "\n";
        echo "Phone: " . ($user['phone'] ?? 'N/A') . "\n";
        echo "Role: " . ($user['role'] ?? 'user') . "\n";
        echo "Active: " . ($user['is_active'] ?? '0') . "\n";
        echo "Created: " . ($user['created_at'] ?? 'N/A') . "\n";
        echo "---\n";
    }
    
} catch(PDOException $exception) {
    echo "Error: " . $exception->getMessage() . "\n";
}
?>