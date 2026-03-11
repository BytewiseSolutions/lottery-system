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
    $drawDate = $data['drawDate'] ?? date('Y-m-d'); // Draw date (when lottery happens)
    
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
    
    // Check if user already voted for this lottery and draw date
    $checkStmt = $db->prepare("SELECT id FROM vote WHERE user_id = ? AND lottery = ? AND draw_date = ?");
    $checkStmt->execute([$userId, $lottery, $drawDate]);
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => "You have already voted for {$lottery} on {$drawDate}"]);
        exit;
    }
    
    // Insert vote with both vote_date (creation) and draw_date (lottery draw)
    $stmt = $db->prepare("INSERT INTO vote (user_id, lottery, numbers, bonus_numbers, vote_date, draw_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $lottery, json_encode($numbers), json_encode($bonusNumbers), date('Y-m-d'), $drawDate]);
    
    echo json_encode(['success' => true, 'message' => 'Vote submitted successfully']);
    
} elseif ($method === 'GET') {
    $user = JWT::authenticate();
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get user's votes with both creation date and draw date
    $stmt = $db->prepare("SELECT id, lottery, numbers, bonus_numbers, vote_date, draw_date, created_at FROM vote WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user['id']]);
    $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($votes as $vote) {
        $result[] = [
            'id' => $vote['id'],
            'lottery' => $vote['lottery'],
            'numbers' => json_decode($vote['numbers']),
            'bonusNumbers' => json_decode($vote['bonus_numbers']),
            'voteDate' => $vote['vote_date'], // When vote was created
            'drawDate' => $vote['draw_date'], // When lottery draw happens
            'createdAt' => $vote['created_at']
        ];
    }
    
    echo json_encode(['votes' => $result]);
}
?>