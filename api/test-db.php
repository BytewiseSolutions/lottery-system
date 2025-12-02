<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Database connection successful!\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "Users count: " . $result['count'] . "\n";
} else {
    echo "Database connection failed!\n";
}
?>