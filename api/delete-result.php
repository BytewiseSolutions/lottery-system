<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

if (!isset($user['role']) || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing result ID']);
        exit;
    }
    
    $query = "DELETE FROM result WHERE id = ?";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$input['id']]);
    
    if ($success) {
        // Log result deletion
        try {
            $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
            $logStmt = $db->prepare($logQuery);
            $logStmt->execute([
                $user['id'],
                'delete_result',
                "Admin deleted result ID: {$input['id']}",
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch(Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
        
        echo json_encode(['success' => true, 'message' => 'Result deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete result']);
    }
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $exception->getMessage()]);
}
?>