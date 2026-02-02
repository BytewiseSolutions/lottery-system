<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Notification ID required']);
    exit;
}

try {
    $query = "UPDATE notification 
              SET is_read = 1 
              WHERE id = :id AND user_id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update notification']);
}
?>
