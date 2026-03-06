<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';

$lottery = $_GET['lottery'] ?? '';
$voteDate = $_GET['voteDate'] ?? date('Y-m-d');

if (empty($lottery)) {
    http_response_code(400);
    echo json_encode(['error' => 'Lottery parameter required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get all votes for the lottery and date
$stmt = $db->prepare("SELECT numbers, bonus_numbers FROM vote WHERE lottery = ? AND vote_date = ?");
$stmt->execute([$lottery, $voteDate]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$numberCounts = [];
$bonusCounts = [];

foreach ($entries as $entry) {
    $numbers = json_decode($entry['numbers'], true);
    $bonusNumbers = json_decode($entry['bonus_numbers'], true);
    
    foreach ($numbers as $num) {
        $numberCounts[$num] = ($numberCounts[$num] ?? 0) + 1;
    }
    
    foreach ($bonusNumbers as $num) {
        $bonusCounts[$num] = ($bonusCounts[$num] ?? 0) + 1;
    }
}

// Get admin allocated votes
$stmt = $db->prepare("SELECT numbers, bonus_numbers, allocated_votes FROM admin_vote WHERE lottery = ? AND vote_date = ?");
$stmt->execute([$lottery, $voteDate]);
$adminEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($adminEntries as $entry) {
    $numbers = json_decode($entry['numbers'], true);
    $bonusNumbers = json_decode($entry['bonus_numbers'], true);
    $allocatedVotes = $entry['allocated_votes'];
    
    foreach ($numbers as $num) {
        $numberCounts[$num] = ($numberCounts[$num] ?? 0) + $allocatedVotes;
    }
    
    foreach ($bonusNumbers as $num) {
        $bonusCounts[$num] = ($bonusCounts[$num] ?? 0) + $allocatedVotes;
    }
}

arsort($numberCounts);
arsort($bonusCounts);

$topNumbers = array_slice($numberCounts, 0, 5, true);
$topBonus = array_slice($bonusCounts, 0, 2, true);

$response = [
    'section1' => array_map(function($num, $count) {
        return ['number' => $num, 'votes' => $count];
    }, array_keys($numberCounts), array_values($numberCounts)),
    'section2' => array_map(function($num, $count) {
        return ['number' => $num, 'votes' => $count];
    }, array_keys($bonusCounts), array_values($bonusCounts))
];

echo json_encode($response);
?>
