<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();
$database = new Database();
$db = $database->getConnection();

try {
    $today = date('Y-m-d');
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM entry WHERE user_id = ? AND DATE(created_at) = ?");
    $stmt->execute([$user['id'], $today]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $dailyLimit = 10;
    $used = $result['count'];
    $remaining = max(0, $dailyLimit - $used);
    
    echo json_encode(['limit' => $dailyLimit, 'used' => $used, 'remaining' => $remaining]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch entry limit']);
}
?>
