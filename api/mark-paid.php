<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

if (!isset($user['role']) || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['winner_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Winner ID required']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $db->beginTransaction();
    
    // Get winner details
    $stmt = $db->prepare("SELECT * FROM winners WHERE id = ?");
    $stmt->execute([$data['winner_id']]);
    $winner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$winner) {
        throw new Exception('Winner not found');
    }
    
    // Update winner status
    $stmt = $db->prepare("UPDATE winners SET status = 'paid', paid_at = NOW() WHERE id = ?");
    $stmt->execute([$data['winner_id']]);
    
    // Create payment record
    $stmt = $db->prepare("
        INSERT INTO payments (winner_id, amount, status, approved_by, approved_at) 
        VALUES (?, ?, 'completed', ?, NOW())
    ");
    $stmt->execute([
        $data['winner_id'],
        $winner['prize_amount'],
        $user['id']
    ]);
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment recorded successfully'
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
