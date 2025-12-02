<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'config/otp.php';

$database = new Database();
$db = $database->getConnection();
$otpHandler = new OTP($db);

$data = json_decode(file_get_contents("php://input"));

if (!$data->fullName || (!$data->email && !$data->phone) || !$data->password || !$data->confirmPassword) {
    http_response_code(400);
    echo json_encode(['error' => 'Full name, email or phone, and password are required']);
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

try {
    // Check if user exists
    $checkQuery = "SELECT id FROM users WHERE email = ? OR phone = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$data->email ?? '', $data->phone ?? '']);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'User with this email or phone already exists']);
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
    
    // Send OTP verifications
    $otpSent = [];
    $otpCodes = []; // For development
    
    if ($data->email) {
        $emailOTP = $otpHandler->generateOTP();
        $otpHandler->saveOTP($userId, $emailOTP, 'email');
        $otpHandler->sendEmailOTP($data->email, $emailOTP);
        $otpSent[] = 'email';
        $otpCodes['email'] = $emailOTP; // For development
    }
    
    if ($data->phone) {
        $phoneOTP = $otpHandler->generateOTP();
        $otpHandler->saveOTP($userId, $phoneOTP, 'phone');
        $otpHandler->sendSMSOTP($data->phone, $phoneOTP);
        $otpSent[] = 'phone';
        $otpCodes['phone'] = $phoneOTP; // For development
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful. Please verify your ' . implode(' and ', $otpSent),
        'userId' => $userId,
        'requiresVerification' => $otpSent,
        'otpCodes' => $otpCodes // Remove this in production
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed']);
}
?>