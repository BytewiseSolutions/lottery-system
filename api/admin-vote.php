<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $user = JWT::authenticate();
    
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $adminId = $user['id'];
    $lottery = $data['lottery'] ?? '';
    $numbers = $data['numbers'] ?? [];
    $bonusNumbers = $data['bonusNumbers'] ?? [];
    $allocatedVotes = $data['allocatedVotes'] ?? 0;
    $voteDate = $data['voteDate'] ?? date('Y-m-d');
    
    if (empty($lottery) || empty($numbers) || empty($bonusNumbers) || $allocatedVotes <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("INSERT INTO admin_vote (admin_id, lottery, numbers, bonus_numbers, allocated_votes, vote_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$adminId, $lottery, json_encode($numbers), json_encode($bonusNumbers), $allocatedVotes, $voteDate]);
    
    echo json_encode(['success' => true, 'message' => 'Votes allocated successfully']);
    
} elseif ($method === 'GET') {
    $user = JWT::authenticate();
    
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("SELECT id, lottery, numbers, bonus_numbers, allocated_votes, vote_date, created_at FROM admin_vote ORDER BY created_at DESC");
    $stmt->execute();
    $adminVotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($adminVotes as $vote) {
        $result[] = [
            'id' => $vote['id'],
            'lottery' => $vote['lottery'],
            'numbers' => json_decode($vote['numbers']),
            'bonusNumbers' => json_decode($vote['bonus_numbers']),
            'allocatedVotes' => $vote['allocated_votes'],
            'voteDate' => $vote['vote_date'],
            'createdAt' => $vote['created_at']
        ];
    }
    
    echo json_encode(['adminVotes' => $result]);
}
?>
