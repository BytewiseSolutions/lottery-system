<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM notification 
              WHERE user_id = :user_id 
              ORDER BY created_at DESC 
              LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $unreadQuery = "SELECT COUNT(*) as count FROM notification 
                    WHERE user_id = :user_id AND is_read = 0";
    $unreadStmt = $db->prepare($unreadQuery);
    $unreadStmt->bindParam(':user_id', $user['id']);
    $unreadStmt->execute();
    $unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'notifications' => $notifications,
        'unread_count' => (int)$unreadCount
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch notifications']);
}
?>
