<?php
require_once 'config/database.php';

// Simulate the upload process
$database = new Database();
$db = $database->getConnection();

// Test data
$data = (object)[
    'lottery' => 'Monday Lotto',
    'drawDate' => '2026-02-09',
    'numbers' => [3, 6, 9, 12, 15],
    'bonusNumbers' => [1, 2],
    'jackpot' => 10.00,
    'publishNow' => true,
    'notes' => 'Test upload'
];

try {
    $db->beginTransaction();
    
    $status = isset($data->publishNow) && $data->publishNow ? 'published' : 'draft';
    
    echo "Step 1: Finding entries...\n";
    $entriesQuery = "SELECT * FROM entries WHERE lottery = ? AND DATE(draw_date) = DATE(?)";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$data->lottery, $data->drawDate]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($entries) . " entries\n\n";
    
    $winners = [];
    $winningNums = $data->numbers;
    $bonusNums = $data->bonusNumbers;
    
    foreach ($entries as $entry) {
        $entryNums = json_decode($entry['numbers'], true);
        $entryBonus = json_decode($entry['bonus_numbers'], true);
        
        $matchedNums = count(array_intersect($entryNums, $winningNums));
        $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
        
        if ($matchedNums == 5 && $matchedBonus == 2) {
            $winners[] = [
                'userId' => $entry['user_id'],
                'entryId' => $entry['id'],
                'matched' => $matchedNums . '+' . $matchedBonus
            ];
        }
    }
    
    echo "Step 2: Inserting result...\n";
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
    
    $resultId = $db->lastInsertId();
    echo "Result ID: $resultId\n";
    echo "Winners count: " . count($winners) . "\n\n";

    if (count($winners) > 0) {
        $prizePerWinner = $data->jackpot / count($winners);
        echo "Step 3: Inserting winners...\n";
        echo "Prize per winner: $prizePerWinner\n";
        
        foreach ($winners as $winner) {
            echo "Inserting winner: User {$winner['userId']}, Entry {$winner['entryId']}\n";
            $insertWinner = "INSERT INTO winners (user_id, lottery, prize_amount, result_id, entry_id, status, draw_date) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
            $stmt = $db->prepare($insertWinner);
            $stmt->execute([$winner['userId'], $data->lottery, $prizePerWinner, $resultId, $winner['entryId'], $data->drawDate]);
            $winnerId = $db->lastInsertId();
            echo "  -> Winner ID: $winnerId\n";
        }
    }
    
    $db->commit();
    echo "\nSuccess! Transaction committed.\n";
    
} catch(PDOException $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
        echo "\nTransaction rolled back!\n";
    }
    echo "ERROR: " . $exception->getMessage() . "\n";
    echo $exception->getTraceAsString() . "\n";
}
?>
