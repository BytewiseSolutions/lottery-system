<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Get users with pagination and search
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $search = $_GET['search'] ?? '';
            
            $offset = ($page - 1) * $limit;
            
            // Build search condition
            $searchCondition = '';
            $params = [];
            if (!empty($search)) {
                $searchCondition = "WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ?";
                $searchTerm = "%{$search}%";
                $params = [$searchTerm, $searchTerm, $searchTerm];
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM users " . $searchCondition;
            $stmt = $db->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get users
            $sql = "SELECT id, full_name, email, phone, email_verified, phone_verified, 
                           is_active, created_at FROM users " . $searchCondition . 
                   " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'users' => $users,
                'total' => (int)$total,
                'totalPages' => ceil($total / $limit),
                'currentPage' => (int)$page
            ]);
            break;
            
        case 'POST':
            // Create new user
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['is_active'] ?? true
            ]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        case 'PUT':
            // Update user
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['is_active'] ?? true,
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            // Delete user
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>