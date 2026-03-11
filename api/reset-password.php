<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->identifier) || !isset($data->code) || !isset($data->newPassword)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if (strlen($data->newPassword) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Get user
    $userQuery = "SELECT id FROM user WHERE email = ? OR phone = ?";
    $userStmt = $db->prepare($userQuery);
    $userStmt->execute([$data->identifier, $data->identifier]);
    
    if ($userStmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }
    
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify reset code
    $codeQuery = "SELECT id FROM password_reset 
                  WHERE user_id = ? AND reset_code = ? AND expires_at > NOW() 
                  ORDER BY created_at DESC LIMIT 1";
    $codeStmt = $db->prepare($codeQuery);
    $codeStmt->execute([$user['id'], $data->code]);
    
    if ($codeStmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or expired reset code']);
        exit;
    }
    
    // Update password
    $hashedPassword = password_hash($data->newPassword, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE user SET password = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$hashedPassword, $user['id']]);
    
    // Delete used reset code
    $deleteQuery = "DELETE FROM password_reset WHERE user_id = ?";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->execute([$user['id']]);
    
    // Log activity
    try {
        $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            $user['id'],
            'password_reset',
            'Password reset successfully',
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch(Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Password reset successfully'
    ]);
    
} catch(PDOException $exception) {
    error_log("Reset password error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to reset password']);
}
?>
