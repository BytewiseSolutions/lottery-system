<?php
require_once 'config/cors.php';
require_once 'config/database.php';

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
    
    $query = "DELETE FROM results WHERE id = ?";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$input['id']]);
    
    if ($success) {
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