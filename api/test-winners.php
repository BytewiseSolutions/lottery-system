<?php
require_once 'config/database.php';

date_default_timezone_set('Africa/Johannesburg');

$database = new Database();
$db = $database->getConnection();

echo "=== Testing Winner Matching ===\n\n";

// Get entries for Friday 2026-01-30
$entriesQuery = "SELECT * FROM entries WHERE lottery = 'Friday Lotto' AND DATE(draw_date) = '2026-01-30'";
$stmt = $db->prepare($entriesQuery);
$stmt->execute();
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($entries) . " entries for Friday Lotto 2026-01-30:\n\n";

foreach ($entries as $entry) {
    echo "Entry ID: {$entry['id']}\n";
    echo "User ID: {$entry['user_id']}\n";
    echo "Numbers: {$entry['numbers']}\n";
    echo "Bonus: {$entry['bonus_numbers']}\n";
    echo "---\n";
}

// Test winning numbers
$winningNums = [2, 4, 6, 8, 10];
$bonusNums = [12, 14];

echo "\n=== Testing Against Winning Numbers ===\n";
echo "Winning Numbers: " . json_encode($winningNums) . "\n";
echo "Bonus Numbers: " . json_encode($bonusNums) . "\n\n";

$winners = [];

foreach ($entries as $entry) {
    $entryNums = json_decode($entry['numbers'], true);
    $entryBonus = json_decode($entry['bonus_numbers'], true);
    
    sort($entryNums);
    sort($entryBonus);
    $sortedWinning = $winningNums;
    $sortedBonus = $bonusNums;
    sort($sortedWinning);
    sort($sortedBonus);
    
    $matchedNums = count(array_intersect($entryNums, $winningNums));
    $matchedBonus = count(array_intersect($entryBonus, $bonusNums));
    
    echo "Entry {$entry['id']}:\n";
    echo "  Entry Numbers: " . json_encode($entryNums) . "\n";
    echo "  Entry Bonus: " . json_encode($entryBonus) . "\n";
    echo "  Matched: {$matchedNums}/5 main, {$matchedBonus}/2 bonus\n";
    echo "  Exact match: " . ($entryNums === $sortedWinning && $entryBonus === $sortedBonus ? "YES ✓" : "NO") . "\n";
    
    if ($matchedNums == 5 && $matchedBonus == 2) {
        $winners[] = $entry['id'];
        echo "  WINNER! ✓✓✓\n";
    }
    echo "\n";
}

echo "=== Summary ===\n";
echo "Total Winners: " . count($winners) . "\n";
if (count($winners) > 0) {
    echo "Winner Entry IDs: " . implode(", ", $winners) . "\n";
}
?>
