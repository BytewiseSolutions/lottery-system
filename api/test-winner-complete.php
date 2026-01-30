<?php
require_once 'config/database.php';

date_default_timezone_set('Africa/Johannesburg');

$database = new Database();
$db = $database->getConnection();

echo "=== Complete Winner Matching Test ===\n\n";

// Step 1: Create test entries
echo "Step 1: Creating test entries...\n";

$testEntries = [
    ['numbers' => [1, 2, 3, 4, 5], 'bonus' => [6, 7], 'should_win' => false],
    ['numbers' => [2, 4, 6, 8, 10], 'bonus' => [12, 14], 'should_win' => true],
    ['numbers' => [2, 4, 6, 8, 10], 'bonus' => [12, 15], 'should_win' => false],
    ['numbers' => [1, 4, 6, 8, 10], 'bonus' => [12, 14], 'should_win' => false],
];

$insertedIds = [];
foreach ($testEntries as $entry) {
    $stmt = $db->prepare("INSERT INTO entries (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (1, 'Monday Lotto', ?, ?, '2026-02-02')");
    $stmt->execute([json_encode($entry['numbers']), json_encode($entry['bonus'])]);
    $insertedIds[] = $db->lastInsertId();
    echo "  Created entry {$db->lastInsertId()}: " . json_encode($entry['numbers']) . " + " . json_encode($entry['bonus']) . 
         " (Should win: " . ($entry['should_win'] ? 'YES' : 'NO') . ")\n";
}

echo "\n";

// Step 2: Test winner matching logic
echo "Step 2: Testing winner matching logic...\n";

$winningNums = [2, 4, 6, 8, 10];
$bonusNums = [12, 14];

echo "Winning Numbers: " . json_encode($winningNums) . "\n";
echo "Bonus Numbers: " . json_encode($bonusNums) . "\n\n";

$entriesQuery = "SELECT * FROM entries WHERE lottery = 'Monday Lotto' AND DATE(draw_date) = '2026-02-02'";
$stmt = $db->prepare($entriesQuery);
$stmt->execute();
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$winners = 0;
foreach ($entries as $entry) {
    $entryNums = json_decode($entry['numbers'], true);
    $entryBonus = json_decode($entry['bonus_numbers'], true);
    
    $matchedNums = count(array_intersect($entryNums, $winningNums));
    $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
    
    echo "Entry {$entry['id']}: ";
    echo json_encode($entryNums) . " + " . json_encode($entryBonus);
    echo " → Matched: {$matchedNums}/5 main, {$matchedBonus}/2 bonus";
    
    if ($matchedNums == 5 && $matchedBonus == 2) {
        $winners++;
        echo " → WINNER ✓";
    }
    echo "\n";
}

echo "\n";
echo "Total Winners Found: $winners\n";
echo "Expected Winners: 1\n";
echo "Test Result: " . ($winners == 1 ? "PASS ✓" : "FAIL ✗") . "\n\n";

// Step 3: Clean up test data
echo "Step 3: Cleaning up test data...\n";
foreach ($insertedIds as $id) {
    $db->prepare("DELETE FROM entries WHERE id = ?")->execute([$id]);
}
echo "Deleted " . count($insertedIds) . " test entries\n";

echo "\n=== Test Complete ===\n";
?>
