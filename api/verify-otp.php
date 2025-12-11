<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/otp.php';

$database = new Database();
$db = $database->getConnection();
$otpHandler = new OTP($db);

$data = json_decode(file_get_contents("php://input"));

if (!$data->userId || !$data->otpCode || !$data->otpType) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID, OTP code, and type are required']);
    exit;
}

try {
    if ($otpHandler->verifyOTP($data->userId, $data->otpCode, $data->otpType)) {
        $column = $data->otpType === 'email' ? 'email_verified' : 'phone_verified';
        $query = "UPDATE users SET $column = TRUE WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->userId]);
        
        $userQuery = "SELECT email_verified, phone_verified, email, phone FROM users WHERE id = ?";
        $userStmt = $db->prepare($userQuery);
        $userStmt->execute([$data->userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        // User is active if at least one contact method is verified
        $hasVerifiedContact = $user['email_verified'] || $user['phone_verified'];
        
        // Check if all provided contact methods are verified
        $isFullyVerified = true;
        if (!empty($user['email']) && !$user['email_verified']) $isFullyVerified = false;
        if (!empty($user['phone']) && !$user['phone_verified']) $isFullyVerified = false;
        
        if ($hasVerifiedContact) {
            $activateQuery = "UPDATE users SET is_active = TRUE WHERE id = ?";
            $activateStmt = $db->prepare($activateQuery);
            $activateStmt->execute([$data->userId]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => ucfirst($data->otpType) . ' verified successfully',
            'fullyVerified' => $isFullyVerified
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or expired OTP']);
    }
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Verification failed']);
}
?>