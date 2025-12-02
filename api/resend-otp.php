<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/otp.php';

$database = new Database();
$db = $database->getConnection();
$otpHandler = new OTP($db);

$data = json_decode(file_get_contents("php://input"));

if (!$data->userId || !$data->otpType) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID and OTP type are required']);
    exit;
}

try {
    // Get user details
    $query = "SELECT email, phone FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->userId]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Generate and send new OTP
    $otpCode = $otpHandler->generateOTP();
    $otpHandler->saveOTP($data->userId, $otpCode, $data->otpType);
    
    if ($data->otpType === 'email' && $user['email']) {
        $otpHandler->sendEmailOTP($user['email'], $otpCode);
    } elseif ($data->otpType === 'phone' && $user['phone']) {
        $otpHandler->sendSMSOTP($user['phone'], $otpCode);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid OTP type or missing contact info']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'OTP resent successfully'
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to resend OTP']);
}
?>