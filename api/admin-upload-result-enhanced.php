<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

// Check if user is admin
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
    
    $resultId = $db->lastInsertId();
    
    // Count winners
    $winnersQuery = "SELECT COUNT(*) as winners FROM entries 
                     WHERE lottery = ? 
                     AND draw_date = DATE(?)
                     AND JSON_CONTAINS(numbers, ?)
                     AND JSON_CONTAINS(bonus_numbers, ?)";
    $stmt = $db->prepare($winnersQuery);
    $stmt->execute([
        $data->lottery,
        $data->drawDate,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers)
    ]);
    $winnersResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $winners = $winnersResult['winners'] ?? 0;
    
    // Update winners count
    $updateQuery = "UPDATE results SET winners = ? WHERE id = ?";
    $db->prepare($updateQuery)->execute([$winners, $resultId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Result uploaded successfully',
        'winners' => $winners
    ]);
    
} catch(PDOException $exception) {
    error_log("Upload result error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload result']);
}
?>
