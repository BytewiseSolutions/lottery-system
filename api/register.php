<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'config/otp.php';

$database = new Database();
$db = $database->getConnection();
$otpHandler = new OTP($db);


$data = json_decode(file_get_contents("php://input"));

error_log("Registration data received: " . json_encode($data));

if (!isset($data->fullName) || (!isset($data->email) && !isset($data->phone)) || !isset($data->password) || !isset($data->confirmPassword)) {
    http_response_code(400);
    $error = ['error' => 'Please fill in all required fields: Full name, email or phone, and password'];
    error_log("Validation failed: " . json_encode($error));
    echo json_encode($error);
    exit;
}

if ($data->password !== $data->confirmPassword) {
    http_response_code(400);
    $error = ['error' => 'Passwords do not match'];
    error_log("Password mismatch");
    echo json_encode($error);
    exit;
}

if (strlen($data->password) < 6) {
    http_response_code(400);
    $error = ['error' => 'Password must be at least 6 characters'];
    error_log("Password too short");
    echo json_encode($error);
    exit;
}

// Validate email format
if (isset($data->email) && $data->email && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    $error = ['error' => 'Invalid email format'];
    error_log("Invalid email format");
    echo json_encode($error);
    exit;
}

try {
    // Check if user exists
    $checkQuery = "SELECT id FROM user WHERE email = ? OR phone = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$data->email ?? '', $data->phone ?? '']);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'An account with this email or phone number already exists. Please try logging in instead.']);
        exit;
    }
    
    // Create user
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
    $query = "INSERT INTO user (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $data->fullName,
        $data->email ?? null,
        $data->phone ?? null,
        $hashedPassword
    ]);
    
    $userId = $db->lastInsertId();
    
    // Log registration activity
    $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    $logStmt = $db->prepare($logQuery);
    $logStmt->execute([
        $userId,
        'user_registered',
        json_encode(['email' => $data->email ?? null, 'phone' => $data->phone ?? null]),
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    // Send OTP verifications asynchronously for better performance
    $otpSent = [];
    
    // Respond to client immediately before sending OTPs
    $response = [
        'success' => true,
        'message' => 'Account created successfully! Verification codes are being sent.',
        'userId' => $userId,
        'requiresVerification' => []
    ];
    
    if ($data->email) {
        $response['requiresVerification'][] = 'email';
        $otpSent[] = 'email';
    }
    
    if ($data->phone) {
        $response['requiresVerification'][] = 'phone';
        $otpSent[] = 'phone';
    }
    
    // Send response immediately
    echo json_encode($response);
    
    // Finish the request to client
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
    
    // Now send OTPs in background
    if ($data->email) {
        $emailOTP = $otpHandler->generateOTP();
        $otpHandler->saveOTP($userId, $emailOTP, 'email');
        $otpHandler->sendEmailOTP($data->email, $emailOTP);
    }
    
    if ($data->phone) {
        $phoneOTP = $otpHandler->generateOTP();
        $otpHandler->saveOTP($userId, $phoneOTP, 'phone');
        $otpHandler->sendSMSOTP($data->phone, $phoneOTP);
    }
    
} catch(PDOException $exception) {
    error_log("Registration error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed. Please try again later.']);
}
?>