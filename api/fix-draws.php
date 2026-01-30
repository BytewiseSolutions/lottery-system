<?php
require_once 'config/database.php';

date_default_timezone_set('Africa/Johannesburg');

$database = new Database();
$db = $database->getConnection();

echo "=== Cleaning Old Draws ===\n";
$deleteStmt = $db->prepare("DELETE FROM upcoming_draws WHERE draw_date <= NOW()");
$deleteStmt->execute();
echo "Deleted " . $deleteStmt->rowCount() . " old draws\n\n";

echo "=== Creating Missing Draws ===\n";
$lotteries = ['Monday Lotto' => 1, 'Wednesday Lotto' => 3, 'Friday Lotto' => 5];

foreach ($lotteries as $lotteryName => $dayOfWeek) {
    $checkStmt = $db->prepare("SELECT id FROM upcoming_draws WHERE lottery = ?");
    $checkStmt->execute([$lotteryName]);
    
    if ($checkStmt->rowCount() === 0) {
        $currentDay = date('N');
        $currentTime = date('H:i:s');
        $drawTime = '19:00:00';
        
        if ($currentDay == $dayOfWeek && $currentTime < $drawTime) {
            $nextDraw = date('Y-m-d 19:00:00');
        } else {
            $daysAhead = ($dayOfWeek - $currentDay + 7) % 7;
            if ($daysAhead == 0) {
                $daysAhead = 7;
            }
            $nextDraw = date('Y-m-d 19:00:00', strtotime("+$daysAhead days"));
        }
        
        $insertStmt = $db->prepare("INSERT INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES (?, ?, 10.00, 'scheduled')");
        $insertStmt->execute([$lotteryName, $nextDraw]);
        echo "✓ Created $lotteryName for $nextDraw\n";
    } else {
        echo "- $lotteryName already exists\n";
    }
}

echo "\n=== Current Upcoming Draws ===\n";
$stmt = $db->prepare("SELECT * FROM upcoming_draws ORDER BY draw_date");
$stmt->execute();
$draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($draws as $draw) {
    echo "- {$draw['lottery']}: {$draw['draw_date']} (Jackpot: \${$draw['jackpot']})\n";
}
echo "Total: " . count($draws) . " draws\n";
?>
