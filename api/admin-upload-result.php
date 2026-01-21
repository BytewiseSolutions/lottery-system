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
    
    // Find matching entries to calculate winners
    $entriesQuery = "SELECT * FROM entries WHERE lottery = ? AND DATE(draw_date) = DATE(?)";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$data->lottery, $data->drawDate]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $winners = [];
    $winningNums = $data->numbers;
    $bonusNums = $data->bonusNumbers;
    
    foreach ($entries as $entry) {
        $entryNums = json_decode($entry['numbers'], true);
        $entryBonus = json_decode($entry['bonus_numbers'], true);
        
        $matchedNums = count(array_intersect($entryNums, $winningNums));
        $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
        
        // Winner criteria: 5 main numbers + 2 bonus (jackpot) OR 5 main + 1 bonus OR 4 main + 2 bonus
        if (($matchedNums == 5 && $matchedBonus == 2) || 
            ($matchedNums == 5 && $matchedBonus >= 1) ||
            ($matchedNums == 4 && $matchedBonus == 2)) {
            $winners[] = [
                'userId' => $entry['user_id'],
                'entryId' => $entry['id'],
                'matched' => $matchedNums . '+' . $matchedBonus
            ];
        }
    }
    
    $query = "INSERT INTO results (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $data->lottery,
        $data->drawDate,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers),
        $data->jackpot,
        count($winners),
        $status,
        $data->notes ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Result uploaded successfully',
        'winners' => count($winners),
        'winnerDetails' => $winners
    ]);
    
} catch(PDOException $exception) {
    error_log("Upload result error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload result', 'details' => $exception->getMessage()]);
}
?>
