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

try {
    switch ($method) {
        case 'GET':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $search = $_GET['search'] ?? '';
            $offset = ($page - 1) * $limit;
            
            $whereClause = '';
            $params = [];
            
            if ($search) {
                $whereClause = 'WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ?';
                $searchTerm = "%$search%";
                $params = [$searchTerm, $searchTerm, $searchTerm];
            }
            
            // Get users
            $stmt = $db->prepare("
                SELECT id, full_name, email, phone, email_verified, phone_verified, 
                       is_active, created_at 
                FROM users 
                $whereClause 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $limit, $offset]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $stmt = $db->prepare("SELECT COUNT(*) FROM users $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetchColumn();
            
            echo json_encode([
                'users' => $users,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("
                INSERT INTO users (full_name, email, phone, password, is_active) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['phone'] ?? null,
                password_hash('defaultpass123', PASSWORD_DEFAULT),
                $data['is_active'] ?? true
            ]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            
            $stmt = $db->prepare("
                UPDATE users 
                SET full_name = ?, email = ?, phone = ?, is_active = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['phone'],
                $data['is_active'],
                $id
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
?>