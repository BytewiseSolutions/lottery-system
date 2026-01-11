<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'config/otp.php';
// require_once 'config/ratelimit.php'; // Disabled for performance

$database = new Database();
$db = $database->getConnection();
$otpHandler = new OTP($db);

// Rate limiting - disabled for performance
// $rateLimit = new RateLimit($db);
// $rateLimit->checkLimit($_SERVER['REMOTE_ADDR'], 'register', 5, 3600);

$data = json_decode(file_get_contents("php://input"));

if (!$data->fullName || (!$data->email && !$data->phone) || !$data->password || !$data->confirmPassword) {
    http_response_code(400);
    echo json_encode(['error' => 'Please fill in all required fields: Full name, email or phone, and password']);
    exit;
}

if ($data->password !== $data->confirmPassword) {
    http_response_code(400);
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

if (strlen($data->password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

// Validate email format
if ($data->email && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

try {
    // Check if user exists
    $checkQuery = "SELECT id FROM users WHERE email = ? OR phone = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$data->email ?? '', $data->phone ?? '']);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'An account with this email or phone number already exists. Please try logging in instead.']);
        exit;
    }
    
    // Create user
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $data->fullName,
        $data->email ?? null,
        $data->phone ?? null,
        $hashedPassword
    ]);
    
    $userId = $db->lastInsertId();
    
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