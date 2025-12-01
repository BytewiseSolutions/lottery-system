<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT e.*, u.email 
              FROM entries e 
              JOIN users u ON e.user_id = u.id 
              ORDER BY e.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($entries);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch entries']);
}
?>