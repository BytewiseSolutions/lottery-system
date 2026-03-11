<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();
$database = new Database();
$db = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $db->prepare("SELECT id, full_name, email, phone, country, email_verified, phone_verified, notification_enabled FROM user WHERE id = ?");
        $stmt->execute([$user['id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($profile);
    } 
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE user SET full_name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$data['full_name'], $data['phone'], $user['id']]);
        echo json_encode(['success' => true]);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
?>
