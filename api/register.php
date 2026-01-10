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
    
    if ($data->email) {
        $emailOTP = $otpHandler->generateOTP();
        $otpHandler->saveOTP($userId, $emailOTP, 'email');
        // Send email OTP in background for faster response
        $otpHandler->sendEmailOTP($data->email, $emailOTP);
        $otpSent[] = 'email';
    }
    
    if ($data->phone) {
        $phoneOTP = $otpHandler->generateOTP();
        $otpHandler->saveOTP($userId, $phoneOTP, 'phone');
        // Send SMS OTP in background for faster response
        $otpHandler->sendSMSOTP($data->phone, $phoneOTP);
        $otpSent[] = 'phone';
    }
    
    $response = [
        'success' => true,
        'message' => 'Account created successfully! Please check your ' . implode(' and ', $otpSent) . ' for verification codes.',
        'userId' => $userId,
        'requiresVerification' => $otpSent
    ];
    
    // Only include OTP codes in development environment
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
        $response['otpCodes'] = [];
        if ($data->email) $response['otpCodes']['email'] = $emailOTP;
        if ($data->phone) $response['otpCodes']['phone'] = $phoneOTP;
    }
    
    echo json_encode($response);
    
} catch(PDOException $exception) {
    error_log("Registration error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed. Please try again later.']);
}
?>