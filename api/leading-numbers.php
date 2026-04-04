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
$stmt = $db->prepare("SELECT numbers, bonus_numbers, allocated_votes, voting_data FROM admin_vote WHERE lottery = ? AND draw_date = ?");
$stmt->execute([$lottery, $voteDate]);
$adminEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($adminEntries as $entry) {
    $numbers = json_decode($entry['numbers'], true);
    $bonusNumbers = json_decode($entry['bonus_numbers'], true);
    $votingData = $entry['voting_data'] ? json_decode($entry['voting_data'], true) : null;
    
    if ($votingData && isset($votingData['mainNumberVotes']) && isset($votingData['bonusNumberVotes'])) {
        // New format: use individual vote amounts from voting_data
        foreach ($votingData['mainNumberVotes'] as $number => $votes) {
            $numberCounts[$number] = ($numberCounts[$number] ?? 0) + $votes;
        }
        
        foreach ($votingData['bonusNumberVotes'] as $number => $votes) {
            $bonusCounts[$number] = ($bonusCounts[$number] ?? 0) + $votes;
        }
    } else {
        // Legacy format: distribute allocated_votes evenly among numbers
        $allocatedVotes = $entry['allocated_votes'];
        $totalNumbers = count($numbers) + count($bonusNumbers);
        $votesPerNumber = $totalNumbers > 0 ? floor($allocatedVotes / $totalNumbers) : 0;
        
        foreach ($numbers as $num) {
            $numberCounts[$num] = ($numberCounts[$num] ?? 0) + $votesPerNumber;
        }
        
        foreach ($bonusNumbers as $num) {
            $bonusCounts[$num] = ($bonusCounts[$num] ?? 0) + $votesPerNumber;
        }
    }
}

arsort($numberCounts);
arsort($bonusCounts);

// Get top 5 main numbers and top 2 bonus numbers (sorted)
$topMainNumbers = array_slice(array_keys($numberCounts), 0, 5, true);
$topBonusNumbers = array_slice(array_keys($bonusCounts), 0, 2, true);

// Sort the top numbers numerically for display
sort($topMainNumbers);
sort($topBonusNumbers);

$response = [
    'lottery' => $lottery,
    'draw_date' => $voteDate,
    'section1' => array_map(function($num, $count) {
        return ['number' => (int)$num, 'votes' => (int)$count];
    }, array_keys($numberCounts), array_values($numberCounts)),
    'section2' => array_map(function($num, $count) {
        return ['number' => (int)$num, 'votes' => (int)$count];
    }, array_keys($bonusCounts), array_values($bonusCounts)),
    'topSection1' => $topMainNumbers,
    'topSection2' => $topBonusNumbers
];

echo json_encode($response);
?>
