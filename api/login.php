<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'config/ratelimit.php';

$database = new Database();
$db = $database->getConnection();

// Rate limiting
$rateLimit = new RateLimit($db);
$rateLimit->checkLimit($_SERVER['REMOTE_ADDR'], 'login', 5, 300); // 5 attempts per 5 minutes

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->identifier) || !isset($data->password) || !$data->identifier || !$data->password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email/phone and password are required']);
    exit;
}

try {
    // Check if identifier is email or phone
    $query = "SELECT * FROM users WHERE (email = ? OR phone = ?) AND is_active = TRUE";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->identifier, $data->identifier]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid credentials or account not verified']);
        exit;
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check for admin role in database
    if (isset($user['role']) && $user['role'] === 'admin' && password_verify($data->password, $user['password'])) {
        $payload = [
            'id' => $user['id'],
            'fullName' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'role' => 'admin',
            'exp' => time() + (24 * 60 * 60)
        ];
        
        $token = JWT::encode($payload);
        
        echo json_encode([
            'success' => true,
            'message' => 'Admin login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'fullName' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'] ?? '',
                'role' => 'admin'
            ]
        ]);
        exit;
    }
    
    // Regular user login
    if (!password_verify($data->password, $user['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
    
    // Generate JWT token
    $payload = [
        'id' => $user['id'],
        'fullName' => $user['full_name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    
    $token = JWT::encode($payload);
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'fullName' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ]
    ]);
    
} catch(PDOException $exception) {
    error_log("Login error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Login failed']);
}
?>