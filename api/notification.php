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
    $query = "SELECT n.id, n.user_id, n.sent_by, n.title, n.message, n.type, n.is_read, n.created_at, COALESCE(u.full_name, 'System') as sent_by_name 
              FROM notification n 
              LEFT JOIN user u ON n.sent_by = u.id 
              ORDER BY n.created_at DESC 
              LIMIT 20";
    
    $stmt = $db->query($query);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($notifications)) {
        echo json_encode([]);
        exit;
    }
    
    // Ensure all fields have values and correct types
    $result = [];
    foreach ($notifications as $notif) {
        // Convert MySQL datetime to ISO 8601 format for JavaScript
        $createdAt = $notif['created_at'] ?: date('Y-m-d H:i:s');
        $dateTime = new DateTime($createdAt);
        
        $result[] = [
            'id' => (int)$notif['id'],
            'user_id' => (int)$notif['user_id'],
            'sent_by' => $notif['sent_by'] ? (int)$notif['sent_by'] : null,
            'title' => (string)($notif['title'] ?: ''),
            'message' => (string)($notif['message'] ?: ''),
            'type' => (string)($notif['type'] ?: 'info'),
            'is_read' => (bool)($notif['is_read'] ?? false),
            'sent_by_name' => (string)($notif['sent_by_name'] ?: 'System'),
            'created_at' => $dateTime->format('c'), // ISO 8601 format
            'sent_count' => 1
        ];
    }
    
    echo json_encode($result);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch notifications', 'details' => $exception->getMessage()]);
}
?>
