<?php
require_once 'config/database.php';
require_once 'config/otp.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';
    
    if (empty($token)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Token required']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    $otp = new OTP($db);
    
    $userId = $otp->verifyEmailToken($token);
    
    if ($userId) {
        echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>