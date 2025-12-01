<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data->lottery || !$data->numbers || !$data->bonusNumbers || !$data->drawDate) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

if (count($data->numbers) !== 5 || count($data->bonusNumbers) !== 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid number selection']);
    exit;
}

try {
    // Convert lottery code to full name
    $lotteryName = $data->lottery === 'mon' ? 'Mon Lotto' : 
                   ($data->lottery === 'wed' ? 'Wed Lotto' : 'Fri Lotto');
    
    // Insert entry
    $query = "INSERT INTO entries (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $user['id'],
        $lotteryName,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers),
        $data->drawDate
    ]);
    
    $entryId = $db->lastInsertId();
    
    // Calculate new pool amount
    $query = "SELECT COUNT(*) * 0.001 as pool_amount FROM entries WHERE lottery = ? AND draw_date = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$lotteryName, $data->drawDate]);
    $poolData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $newPoolAmount = $poolData['pool_amount'] ?: 0.001;
    
    echo json_encode([
        'success' => true,
        'message' => 'Entry submitted successfully!',
        'newPoolAmount' => number_format($newPoolAmount, 3),
        'entryId' => $entryId
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit entry']);
}
?>