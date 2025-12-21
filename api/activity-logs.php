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

// Create activity_logs table if it doesn't exist
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )
    ");
} catch (Exception $e) {
    // Table might already exist
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 20;
            $filter = $_GET['filter'] ?? '';
            $offset = ($page - 1) * $limit;
            
            $whereClause = '';
            $params = [];
            
            if ($filter) {
                $whereClause = 'WHERE al.action LIKE ?';
                $params = ["%$filter%"];
            }
            
            $stmt = $db->prepare("
                SELECT al.*, u.full_name, u.email 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                $whereClause 
                ORDER BY al.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $limit, $offset]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $stmt = $db->prepare("SELECT COUNT(*) FROM activity_logs al $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetchColumn();
            
            echo json_encode([
                'logs' => $logs,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("
                INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['user_id'] ?? null,
                $data['action'],
                $data['description'] ?? '',
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            echo json_encode(['success' => true]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
?>