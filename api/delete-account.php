<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data->userId) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit;
}

try {

    $query = "UPDATE user SET is_active = 0 WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->userId]);
    
    try {
        $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            $data->userId,
            'account_deleted',
            json_encode(['reason' => 'user_requested']),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch(Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Account deleted successfully'
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete account']);
}
?>
