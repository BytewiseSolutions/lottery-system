<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $user = JWT::authenticate();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $user['id'];
    $lottery = $data['lottery'] ?? '';
    $numbers = $data['numbers'] ?? [];
    $bonusNumbers = $data['bonusNumbers'] ?? [];
    $voteDate = $data['voteDate'] ?? date('Y-m-d');
    
    if (empty($lottery) || empty($numbers) || empty($bonusNumbers)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    if (count($numbers) !== 5 || count($bonusNumbers) !== 2) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid number selection']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("INSERT INTO vote (user_id, lottery, numbers, bonus_numbers, vote_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $lottery, json_encode($numbers), json_encode($bonusNumbers), $voteDate]);
    
    echo json_encode(['success' => true, 'message' => 'Vote submitted successfully']);
}
?>
