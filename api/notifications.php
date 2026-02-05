<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

if (!isset($user['role']) || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT n.*, u.full_name as sent_by_name 
              FROM notifications n 
              JOIN user u ON n.sent_by = u.id 
              ORDER BY n.created_at DESC 
              LIMIT 20";
    
    $stmt = $db->query($query);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($notifications);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch notifications']);
}
?>
