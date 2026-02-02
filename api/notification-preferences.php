<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $db->prepare("UPDATE user SET notification_enabled = ? WHERE id = ?");
    $stmt->execute([$data['enabled'] ? 1 : 0, $user['id']]);
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update preferences']);
}
?>
