<?php
require_once __DIR__ . '/config/database.php';

date_default_timezone_set('Africa/Johannesburg');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $archiveQuery = "INSERT INTO past_draw (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status)
                     SELECT lottery, draw_date, '[]', '[]', jackpot, 0, 'completed'
                     FROM upcoming_draw 
                     WHERE draw_date <= NOW()";
    $db->prepare($archiveQuery)->execute();
    
    $deleteQuery = "DELETE FROM upcoming_draw WHERE draw_date <= NOW()";
    $db->prepare($deleteQuery)->execute();
    
    $checkQuery = "SELECT lottery FROM upcoming_draw";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $existingDraws = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $lotteries = ['Monday Lotto' => 1, 'Wednesday Lotto' => 3, 'Friday Lotto' => 5];
    
    foreach ($lotteries as $lotteryName => $dayOfWeek) {
        if (!in_array($lotteryName, $existingDraws)) {
            $currentDay = date('N');
            $currentTime = date('H:i:s');
            $drawTime = '19:00:00';
            
            if ($currentDay == $dayOfWeek && $currentTime < $drawTime) {
                $nextDraw = date('Y-m-d 19:00:00');
            } else {
                $daysAhead = ($dayOfWeek - $currentDay + 7) % 7;
                if ($daysAhead == 0) $daysAhead = 7;
                $nextDraw = date('Y-m-d 19:00:00', strtotime("+$daysAhead days"));
            }
            
            $stmt = $db->prepare("INSERT INTO upcoming_draw (lottery, draw_date, jackpot, status) VALUES (?, ?, 10.00, 'scheduled')");
            $stmt->execute([$lotteryName, $nextDraw]);
            error_log("Auto-generated draw: $lotteryName on $nextDraw");
        }
    }
    
    echo "Draws generated successfully\n";
    
} catch(Exception $e) {
    error_log("Cron error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
?>
