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
    
    $query = "INSERT INTO result (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes) 
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
    
    $entriesQuery = "SELECT * FROM entry WHERE lottery = ? AND DATE(draw_date) = DATE(?)";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$data->lottery, $data->drawDate]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $winners = 0;
    $winningNums = $data->numbers;
    $bonusNums = $data->bonusNumbers;
    
    foreach ($entries as $entry) {
        $entryNums = json_decode($entry['numbers'], true);
        $entryBonus = json_decode($entry['bonus_numbers'], true);
        
        sort($entryNums);
        sort($entryBonus);
        sort($winningNums);
        sort($bonusNums);
        
        if ($entryNums === $winningNums && $entryBonus === $bonusNums) {
            $winners++;
        }
    }
    
    $updateQuery = "UPDATE result SET winners = ? WHERE id = ?";
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
