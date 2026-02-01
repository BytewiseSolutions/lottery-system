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
    $query = "SELECT w.*, u.full_name, u.email, u.phone 
              FROM winner w 
              JOIN user u ON w.user_id = u.id 
              ORDER BY w.created_at DESC";
    
    $stmt = $db->query($query);
    $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($winners);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch winners']);
}
?>
