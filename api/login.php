<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data->identifier || !$data->password) {
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
    http_response_code(500);
    echo json_encode(['error' => 'Login failed']);
}
?>