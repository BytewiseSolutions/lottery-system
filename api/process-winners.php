<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get the result
$resultId = 17;
$result = $db->query("SELECT * FROM result WHERE id = $resultId")->fetch(PDO::FETCH_ASSOC);

echo "Processing result ID: $resultId\n";
echo "Lottery: {$result['lottery']}\n";
echo "Draw Date: {$result['draw_date']}\n";
echo "Winning Numbers: {$result['winning_numbers']}\n";
echo "Bonus Numbers: {$result['bonus_numbers']}\n\n";

// Get entries for this draw
$entriesQuery = "SELECT * FROM entry WHERE lottery = ? AND DATE(draw_date) = DATE(?)";
$stmt = $db->prepare($entriesQuery);
$stmt->execute([$result['lottery'], $result['draw_date']]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($entries) . " entries\n\n";

$winners = [];
$winningNums = json_decode($result['winning_numbers'], true);
$bonusNums = json_decode($result['bonus_numbers'], true);

foreach ($entries as $entry) {
    $entryNums = json_decode($entry['numbers'], true);
    $entryBonus = json_decode($entry['bonus_numbers'], true);
    
    $matchedNums = count(array_intersect($entryNums, $winningNums));
    $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
    
    echo "Entry ID {$entry['id']} (User {$entry['user_id']}): ";
    echo "Numbers: " . json_encode($entryNums) . " ";
    echo "Bonus: " . json_encode($entryBonus) . " ";
    echo "Matched: {$matchedNums}+{$matchedBonus}\n";
    
    // Winner criteria: Must match ALL 5 main numbers AND ALL 2 bonus numbers
    if ($matchedNums == 5 && $matchedBonus == 2) {
        echo "  -> WINNER!\n";
        $winners[] = [
            'userId' => $entry['user_id'],
            'entryId' => $entry['id'],
            'matched' => $matchedNums . '+' . $matchedBonus
        ];
    }
}

echo "\nTotal winners: " . count($winners) . "\n\n";

if (count($winners) > 0) {
    $prizePerWinner = $result['jackpot'] / count($winners);
    echo "Prize per winner: $prizePerWinner\n\n";
    
    foreach ($winners as $winner) {
        echo "Inserting winner: User {$winner['userId']}, Entry {$winner['entryId']}\n";
        try {
            $insertWinner = "INSERT INTO winner (user_id, lottery, prize_amount, result_id, entry_id, status, draw_date) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
            $stmt = $db->prepare($insertWinner);
            $stmt->execute([
                $winner['userId'], 
                $result['lottery'], 
                $prizePerWinner, 
                $resultId, 
                $winner['entryId'], 
                $result['draw_date']
            ]);
            $winnerId = $db->lastInsertId();
            echo "  -> Winner inserted with ID: $winnerId\n";
        } catch(PDOException $e) {
            echo "  -> ERROR: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nDone!\n";
?>
