<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->identifier) || empty($data->identifier)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email or phone number is required']);
    exit;
}

try {
    $query = "SELECT id, email, phone FROM user WHERE email = ? OR phone = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->identifier, $data->identifier]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'No account found with this email or phone number']);
        exit;
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $resetCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $insertQuery = "INSERT INTO password_reset (user_id, reset_code, expires_at, created_at) 
                    VALUES (?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE reset_code = ?, expires_at = ?, created_at = NOW()";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([$user['id'], $resetCode, $expiresAt, $resetCode, $expiresAt]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Reset code sent successfully',
        'debug_code' => $resetCode 
    ]);
    
} catch(PDOException $exception) {
    error_log("Send reset code error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send reset code']);
}
?>
