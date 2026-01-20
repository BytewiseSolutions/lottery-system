<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

if (!isset($user['role']) || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data->lottery || !$data->drawDate || !$data->jackpot || !$data->numbers || !$data->bonusNumbers) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $status = isset($data->publishNow) && $data->publishNow ? 'published' : 'draft';
    
    $query = "INSERT INTO results (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes) 
              VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $data->lottery,
        $data->drawDate,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers),
        $data->jackpot,
        $status,
        $data->notes ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Result uploaded successfully',
        'winners' => 0
    ]);
    
} catch(PDOException $exception) {
    error_log("Upload result error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload result', 'details' => $exception->getMessage()]);
}
?>
