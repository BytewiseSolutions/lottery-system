<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $archiveQuery = "INSERT INTO past_draw (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status)
                     SELECT lottery, draw_date, '[]', '[]', jackpot, 0, 'completed'
                     FROM upcoming_draw 
                     WHERE draw_date <= NOW()";
    try {
        $db->prepare($archiveQuery)->execute();
    } catch(PDOException $e) {

    }
    
    $deleteQuery = "DELETE FROM upcoming_draw WHERE draw_date <= NOW()";
    $db->prepare($deleteQuery)->execute();
    
    // Check how many upcoming draws exist
    $checkQuery = "SELECT lottery, draw_date FROM upcoming_draw";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $existingDraws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Existing draws count: " . count($existingDraws));
    error_log("Existing draws: " . json_encode($existingDraws));
    
    $lotteries = ['Monday Lotto' => 1, 'Wednesday Lotto' => 3, 'Friday Lotto' => 5];
    
    foreach ($lotteries as $lotteryName => $dayOfWeek) {
        // Check if this lottery already has an upcoming draw
        $hasUpcoming = false;
        foreach ($existingDraws as $draw) {
            if ($draw['lottery'] === $lotteryName) {
                $hasUpcoming = true;
                break;
            }
        }
        
        error_log("Checking $lotteryName: hasUpcoming=" . ($hasUpcoming ? 'true' : 'false'));
        
        if (!$hasUpcoming) {
            // Calculate next draw date
            $currentDay = date('N');
            $currentTime = date('H:i:s');
            $drawTime = '19:00:00';
            
            error_log("Current day: $currentDay, Current time: $currentTime, Target day: $dayOfWeek");
            
            // If it's the same day but before draw time, use today
            if ($currentDay == $dayOfWeek && $currentTime < $drawTime) {
                $nextDraw = date('Y-m-d 19:00:00');
            } else {
                // Otherwise, get next occurrence of this day
                $daysAhead = ($dayOfWeek - $currentDay + 7) % 7;
                if ($daysAhead == 0) {
                    $daysAhead = 7; // If same day but after draw time, go to next week
                }
                $nextDraw = date('Y-m-d 19:00:00', strtotime("+$daysAhead days"));
            }
            
            error_log("Creating new draw: $lotteryName on $nextDraw");
            try {
                $stmt = $db->prepare("INSERT INTO upcoming_draw (lottery, draw_date, jackpot, status) VALUES (?, ?, 10.00, 'scheduled')");
                $stmt->execute([$lotteryName, $nextDraw]);
                error_log("Successfully created draw for $lotteryName");
            } catch(PDOException $e) {
                error_log("Failed to create draw for $lotteryName: " . $e->getMessage());
            }
        }
    }
    
    $query = "SELECT lottery as name, draw_date, jackpot 
              FROM upcoming_draw 
              ORDER BY draw_date";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'draws' => $draws]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>