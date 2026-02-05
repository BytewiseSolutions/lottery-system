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
    $db->beginTransaction();
    
    $status = isset($data->publishNow) && $data->publishNow ? 'published' : 'draft';
    
    error_log("UPLOAD STARTED: " . date('Y-m-d H:i:s'));
    file_put_contents('/tmp/lottery-upload.log', date('Y-m-d H:i:s') . " - Upload started\n", FILE_APPEND);
    file_put_contents('/tmp/lottery-upload.log', "Data: " . json_encode($data) . "\n", FILE_APPEND);
    
    $entriesQuery = "SELECT * FROM entry WHERE lottery = ? AND DATE(draw_date) = DATE(?)";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$data->lottery, $data->drawDate]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    file_put_contents('/tmp/lottery-upload.log', "Found " . count($entries) . " entries\n", FILE_APPEND);
    
    $winners = [];
    $winningNums = $data->numbers;
    $bonusNums = $data->bonusNumbers;
    
    foreach ($entries as $entry) {
        $entryNums = json_decode($entry['numbers'], true);
        $entryBonus = json_decode($entry['bonus_numbers'], true);
        
        $matchedNums = count(array_intersect($entryNums, $winningNums));
        $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
        
        // Winner criteria: Must match ALL 5 main numbers AND ALL 2 bonus numbers
        if ($matchedNums == 5 && $matchedBonus == 2) {
            $winners[] = [
                'userId' => $entry['user_id'],
                'entryId' => $entry['id'],
                'matched' => $matchedNums . '+' . $matchedBonus
            ];
        }
    }
    
    $query = "INSERT INTO result (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes) 
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
    
    $resultId = $db->lastInsertId();
    
    file_put_contents('/tmp/lottery-upload.log', "Result ID: $resultId, Winners: " . count($winners) . "\n", FILE_APPEND);

    if (count($winners) > 0) {
        $prizePerWinner = $data->jackpot / count($winners);
        file_put_contents('/tmp/lottery-upload.log', "Prize per winner: $prizePerWinner\n", FILE_APPEND);
        
        foreach ($winners as $winner) {
            file_put_contents('/tmp/lottery-upload.log', "Inserting winner: " . json_encode($winner) . "\n", FILE_APPEND);
            try {
                $insertWinner = "INSERT INTO winner (user_id, lottery, prize_amount, result_id, entry_id, status, draw_date) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
                $stmt = $db->prepare($insertWinner);
                $result = $stmt->execute([$winner['userId'], $data->lottery, $prizePerWinner, $resultId, $winner['entryId'], $data->drawDate]);
                $winnerId = $db->lastInsertId();
                file_put_contents('/tmp/lottery-upload.log', "Winner inserted with ID: $winnerId\n", FILE_APPEND);
            } catch(PDOException $e) {
                file_put_contents('/tmp/lottery-upload.log', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                throw $e;
            }
        }
    } else {
        file_put_contents('/tmp/lottery-upload.log', "No winners found\n", FILE_APPEND);
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Result uploaded successfully',
        'winners' => count($winners),
        'winnerDetails' => $winners
    ]);
    
} catch(PDOException $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Upload result error: " . $exception->getMessage());
    file_put_contents('/tmp/lottery-upload.log', "EXCEPTION: " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload result', 'details' => $exception->getMessage()]);
}
?>
