<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data->email || !$data->password || !$data->confirmPassword) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
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
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->email]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'User already exists']);
        exit;
    }
    
    // Create user
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->email, $hashedPassword]);
    
    $userId = $db->lastInsertId();
    
    // Generate JWT token
    $payload = [
        'id' => $userId,
        'email' => $data->email,
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    
    $token = JWT::encode($payload);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'token' => $token,
        'user' => ['id' => $userId, 'email' => $data->email]
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed']);
}
?>