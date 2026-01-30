<?php
require_once 'config/database.php';

date_default_timezone_set('Africa/Johannesburg');

$database = new Database();
$db = $database->getConnection();

echo "=== Current Time ===\n";
echo "Now: " . date('Y-m-d H:i:s') . "\n";
echo "Day of week: " . date('N') . " (" . date('l') . ")\n\n";

echo "=== Upcoming Draws ===\n";
$stmt = $db->prepare("SELECT * FROM upcoming_draws ORDER BY draw_date");
$stmt->execute();
$draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($draws as $draw) {
    echo "- {$draw['lottery']}: {$draw['draw_date']} (Jackpot: \${$draw['jackpot']})\n";
}
echo "Total: " . count($draws) . " draws\n\n";

echo "=== Testing Auto-Create Logic ===\n";
$lotteries = ['Monday Lotto' => 1, 'Wednesday Lotto' => 3, 'Friday Lotto' => 5];

foreach ($lotteries as $lotteryName => $dayOfWeek) {
    $hasUpcoming = false;
    foreach ($draws as $draw) {
        if ($draw['lottery'] === $lotteryName) {
            $hasUpcoming = true;
            break;
        }
    }
    
    echo "$lotteryName (Day $dayOfWeek): ";
    if ($hasUpcoming) {
        echo "Already exists\n";
    } else {
        echo "MISSING - Should create!\n";
        
        $currentDay = date('N');
        $currentTime = date('H:i:s');
        $drawTime = '19:00:00';
        
        if ($currentDay == $dayOfWeek && $currentTime < $drawTime) {
            $nextDraw = date('Y-m-d 19:00:00');
        } else {
            $dayName = array_search($dayOfWeek, [1 => 'monday', 3 => 'wednesday', 5 => 'friday']);
            $nextDraw = date('Y-m-d 19:00:00', strtotime('next ' . $dayName));
        }
        
        echo "  Would create for: $nextDraw\n";
    }
}

echo "\n=== Manually Trigger Creation ===\n";
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
            $dayName = array_search($dayOfWeek, [1 => 'monday', 3 => 'wednesday', 5 => 'friday']);
            $nextDraw = date('Y-m-d 19:00:00', strtotime('next ' . $dayName));
        }
        
        try {
            $insertStmt = $db->prepare("INSERT INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES (?, ?, 10.00, 'scheduled')");
            $insertStmt->execute([$lotteryName, $nextDraw]);
            echo "✓ Created $lotteryName for $nextDraw\n";
        } catch(PDOException $e) {
            echo "✗ Failed to create $lotteryName: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Final Upcoming Draws ===\n";
$stmt = $db->prepare("SELECT * FROM upcoming_draws ORDER BY draw_date");
$stmt->execute();
$draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($draws as $draw) {
    echo "- {$draw['lottery']}: {$draw['draw_date']} (Jackpot: \${$draw['jackpot']})\n";
}
echo "Total: " . count($draws) . " draws\n";
?>
