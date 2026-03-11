<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
// require_once 'config/ratelimit.php'; // Disabled for faster response

$database = new Database();
$db = $database->getConnection();

// Rate limiting - disabled for performance
// $rateLimit = new RateLimit($db);
// $rateLimit->checkLimit($_SERVER['REMOTE_ADDR'], 'login', 5, 300);

$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request format']);
    exit;
}

if (!isset($data->identifier) || !isset($data->password) || 
    trim($data->identifier) === '' || trim($data->password) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Email/phone and password are required']);
    exit;
}

try {
    $query = "SELECT id, full_name, email, phone, country, profile_picture, password, role FROM user WHERE (email = ? OR phone = ?) AND is_active = TRUE LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->identifier, $data->identifier]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Build profile picture URL if exists
    $profilePictureUrl = null;
    if ($user['profile_picture']) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . '://' . $host . dirname($_SERVER['PHP_SELF']);
        $profilePictureUrl = $baseUrl . '/get-file.php?id=' . $user['profile_picture'];
    }
    
    if (!password_verify($data->password, $user['password'])) {
        try {
            $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
            $logStmt = $db->prepare($logQuery);
            $logStmt->execute([
                $user['id'],
                'login_failed',
                "Failed login attempt - Invalid password",
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch(Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
        
        http_response_code(400);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
    
    $payload = [
        'id' => $user['id'],
        'fullName' => $user['full_name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? '',
        'country' => $user['country'] ?? null,
        'profilePicture' => $profilePictureUrl,
        'exp' => time() + (24 * 60 * 60)
    ];
    
    if (isset($user['role']) && $user['role'] === 'admin') {
        $payload['role'] = 'admin';
        $message = 'Admin login successful';
        $userResponse = [
            'id' => $user['id'],
            'fullName' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'country' => $user['country'] ?? null,
            'profilePicture' => $profilePictureUrl,
            'role' => 'admin'
        ];
    } else {
        $message = 'Login successful';
        $userResponse = [
            'id' => $user['id'],
            'fullName' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'country' => $user['country'] ?? null,
            'profilePicture' => $profilePictureUrl
        ];
    }
    
    $token = JWT::encode($payload);
    
    try {
        $role = isset($user['role']) && $user['role'] === 'admin' ? 'Admin' : 'User';
        $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            $user['id'],
            'login',
            "$role logged in successfully",
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch(Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'token' => $token,
        'user' => $userResponse
    ]);
    
} catch(PDOException $exception) {
    error_log("Login error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Login failed']);
}
?>