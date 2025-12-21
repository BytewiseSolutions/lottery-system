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

// Create api_keys table if it doesn't exist
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS api_keys (
            id INT AUTO_INCREMENT PRIMARY KEY,
            key_name VARCHAR(100) NOT NULL,
            api_key VARCHAR(64) UNIQUE NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            last_used TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (Exception $e) {
    // Table might already exist
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $stmt = $db->prepare("
                SELECT id, key_name, api_key, is_active, last_used, created_at 
                FROM api_keys 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($keys);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $apiKey = bin2hex(random_bytes(32));
            
            $stmt = $db->prepare("
                INSERT INTO api_keys (key_name, api_key) 
                VALUES (?, ?)
            ");
            $stmt->execute([$data['name'], $apiKey]);
            
            echo json_encode([
                'success' => true,
                'api_key' => $apiKey,
                'id' => $db->lastInsertId()
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("UPDATE api_keys SET is_active = ? WHERE id = ?");
            $stmt->execute([$data['is_active'], $data['id']]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("DELETE FROM api_keys WHERE id = ?");
            $stmt->execute([$data['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
?>