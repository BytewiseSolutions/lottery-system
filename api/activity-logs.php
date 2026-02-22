<?php
require_once 'config/database.php';
require_once 'config/jwt.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user = JWT::authenticate();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    // Check if user is admin
    if ($user['role'] !== 'admin' && $user['email'] !== 'admin@totalfreelotto.com') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $stmt = $db->prepare("
            SELECT 
                al.*,
                u.full_name as user_name
            FROM activity_log al
            LEFT JOIN user u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT 500
        ");
        
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($logs);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
