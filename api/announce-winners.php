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

if (!$data->drawId || !$data->winningNumbers || !$data->bonusNumbers) {
    http_response_code(400);
    echo json_encode(['error' => 'Draw ID and winning numbers required']);
    exit;
}

try {
    // Get draw details
    $drawQuery = "SELECT * FROM upcoming_draws WHERE id = ?";
    $stmt = $db->prepare($drawQuery);
    $stmt->execute([$data->drawId]);
    $draw = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$draw) {
        http_response_code(404);
        echo json_encode(['error' => 'Draw not found']);
        exit;
    }
    
    // Find matching entries
    $entriesQuery = "SELECT * FROM entries WHERE lottery = ? AND draw_date = ?";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$draw['lottery'], $draw['draw_date']]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $winners = [];
    $winningNums = $data->winningNumbers;
    $bonusNums = $data->bonusNumbers;
    
    foreach ($entries as $entry) {
        $entryNums = json_decode($entry['numbers'], true);
        $entryBonus = json_decode($entry['bonus_numbers'], true);
        
        $matchedNums = count(array_intersect($entryNums, $winningNums));
        $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
        
        // Winner criteria: 5 main numbers + 2 bonus OR 5 main numbers + 1 bonus
        if (($matchedNums == 5 && $matchedBonus == 2) || ($matchedNums == 5 && $matchedBonus >= 1)) {
            $winners[] = [
                'userId' => $entry['user_id'],
                'entryId' => $entry['id'],
                'matched' => $matchedNums . '+' . $matchedBonus
            ];
        }
    }
    
    // Calculate prize pool
    $prizePool = (count($entries) * 0.01) + 10;
    $prizePerWinner = count($winners) > 0 ? $prizePool / count($winners) : 0;
    
    // Insert result
    $insertResult = "INSERT INTO results (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status) 
                     VALUES (?, ?, ?, ?, ?, ?, 'published')";
    $stmt = $db->prepare($insertResult);
    $stmt->execute([
        $draw['lottery'],
        $draw['draw_date'],
        json_encode($winningNums),
        json_encode($bonusNums),
        '$' . number_format($prizePool, 2),
        count($winners)
    ]);
    
    // Update draw status
    $updateDraw = "UPDATE upcoming_draws SET status = 'completed' WHERE id = ?";
    $stmt = $db->prepare($updateDraw);
    $stmt->execute([$data->drawId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Winners announced successfully',
        'totalEntries' => count($entries),
        'totalWinners' => count($winners),
        'prizePool' => '$' . number_format($prizePool, 2),
        'prizePerWinner' => '$' . number_format($prizePerWinner, 2),
        'winners' => $winners
    ]);
    
} catch(PDOException $exception) {
    error_log("Announce winners error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to announce winners']);
}
?>
