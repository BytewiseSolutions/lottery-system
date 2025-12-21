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

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['drawDate'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    $id = $input['id'];
    $drawDate = $input['drawDate'];
    $jackpot = $input['jackpot'] ?? null;
    
    // Update the draw time in upcoming_draws table
    if ($jackpot) {
        $query = "UPDATE upcoming_draws SET draw_date = ?, jackpot = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$drawDate, $jackpot, $id]);
    } else {
        $query = "UPDATE upcoming_draws SET draw_date = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$drawDate, $id]);
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Draw updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update draw']);
    }
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $exception->getMessage()]);
}
?>