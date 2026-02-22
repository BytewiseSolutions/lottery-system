<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data->fullName || (!$data->email && !$data->phone) || !$data->password) {
    http_response_code(400);
    echo json_encode(['error' => 'Please fill in all required fields: Full name, email OR phone, and password']);
    exit;
}

if (strlen($data->password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

if ($data->email && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

try {
    $checkQuery = "SELECT id FROM user WHERE email = ? OR phone = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$data->email ?? '', $data->phone ?? '']);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'An account with this email or phone number already exists. Please try logging in instead.']);
        exit;
    }
    
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
    $query = "INSERT INTO user (full_name, email, phone, password, is_active) VALUES (?, ?, ?, ?, 1)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $data->fullName,
        $data->email ?? null,
        $data->phone ?? null,
        $hashedPassword
    ]);
    
    $userId = $db->lastInsertId();
    
    $userQuery = "SELECT id, full_name, email, phone, role FROM user WHERE id = ?";
    $userStmt = $db->prepare($userQuery);
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    $payload = [
        'user_id' => $user['id'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role'] ?? 'user',
        'exp' => time() + (24 * 60 * 60)
    ];
    $token = JWT::encode($payload);
    
    // Log registration
    try {
        $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            $userId,
            'register',
            "New user registered: {$data->fullName}",
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch(Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully!',
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'] ?? 'user'
        ]
    ]);
    
} catch(PDOException $exception) {
    error_log("Registration error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed. Please try again later.']);
}
?>