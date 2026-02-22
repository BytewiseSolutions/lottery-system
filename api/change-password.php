<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $db->prepare("SELECT password FROM user WHERE id = ?");
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!password_verify($data['currentPassword'], $row['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Current password is incorrect']);
        exit;
    }
    
    $newHash = password_hash($data['newPassword'], PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE user SET password = ? WHERE id = ?");
    $stmt->execute([$newHash, $user['id']]);
    
    // Log password change
    try {
        $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            $user['id'],
            'password_change',
            "User changed their password",
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch(Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
    
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to change password']);
}
?>
