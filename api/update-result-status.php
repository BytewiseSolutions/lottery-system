<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id']) || !isset($input['status'])) {
        throw new Exception('Missing required fields: id and status');
    }
    
    $id = intval($input['id']);
    $status = $input['status'];
    
    // Validate status
    if (!in_array($status, ['published', 'draft'])) {
        throw new Exception('Invalid status. Must be "published" or "draft"');
    }
    
    $query = "UPDATE results SET status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$status, $id]);
    
    if ($success && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "Result status updated to $status"
        ]);
    } else {
        throw new Exception('Result not found or no changes made');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>