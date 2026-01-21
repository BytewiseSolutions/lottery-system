<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Archive draws that are 1 hour past their draw time (at 20:00 for 19:00 draws)
    $archiveQuery = "INSERT INTO past_draws (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status)
                     SELECT lottery, draw_date, '[]', '[]', jackpot, 0, 'completed'
                     FROM upcoming_draws 
                     WHERE draw_date < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    try {
        $db->prepare($archiveQuery)->execute();
    } catch(PDOException $e) {
        // Ignore duplicate errors
    }
    
    // Delete archived draws (1 hour after draw time)
    $deleteQuery = "DELETE FROM upcoming_draws WHERE draw_date < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $db->prepare($deleteQuery)->execute();
    
    // Check how many upcoming draws exist
    $checkQuery = "SELECT lottery, draw_date FROM upcoming_draws";
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
            
            if ($currentDay == $dayOfWeek && $currentTime < $drawTime) {
                $nextDraw = date('Y-m-d 19:00:00');
            } else {
                $dayName = array_search($dayOfWeek, [1 => 'monday', 3 => 'wednesday', 5 => 'friday']);
                $nextDraw = date('Y-m-d 19:00:00', strtotime('next ' . $dayName));
            }
            
            error_log("Creating new draw: $lotteryName on $nextDraw");
            try {
                $stmt = $db->prepare("INSERT INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES (?, ?, 10.00, 'scheduled')");
                $stmt->execute([$lotteryName, $nextDraw]);
                error_log("Successfully created draw for $lotteryName");
            } catch(PDOException $e) {
                error_log("Failed to create draw for $lotteryName: " . $e->getMessage());
            }
        }
    }
    
    $query = "SELECT lottery as lottery_type, draw_date, jackpot 
              FROM upcoming_draws 
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