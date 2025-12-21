<?php
require_once 'config/database.php';
require_once 'config/cors.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'];
    $ids = $data['ids'];
    
    if (empty($ids) || !is_array($ids)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid IDs provided']);
        exit;
    }
    
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    switch ($action) {
        case 'delete':
            $stmt = $db->prepare("DELETE FROM results WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $affected = $stmt->rowCount();
            break;
            
        case 'publish':
            // Assuming there's a status field
            $stmt = $db->prepare("UPDATE results SET status = 'published' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $affected = $stmt->rowCount();
            break;
            
        case 'unpublish':
            $stmt = $db->prepare("UPDATE results SET status = 'draft' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $affected = $stmt->rowCount();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            exit;
    }
    
    echo json_encode([
        'success' => true,
        'affected' => $affected,
        'message' => "Successfully processed $affected items"
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Bulk operation failed']);
}
?>