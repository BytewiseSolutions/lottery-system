<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $archiveQuery = "INSERT INTO past_draws (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status)
                     SELECT lottery, draw_date, '[]', '[]', jackpot, 0, 'completed'
                     FROM upcoming_draws 
                     WHERE draw_date < NOW()";
    try {
        $db->prepare($archiveQuery)->execute();
    } catch(PDOException $e) {
        // Ignore duplicate errors
    }
    
    $deleteQuery = "DELETE FROM upcoming_draws WHERE draw_date < NOW()";
    $db->prepare($deleteQuery)->execute();
    
    $checkQuery = "SELECT COUNT(*) as count FROM upcoming_draws WHERE draw_date >= NOW()";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count < 3) {
        // Get current day of week (1=Monday, 7=Sunday)
        $currentDay = date('N');
        $currentTime = date('H:i:s');
        $drawTime = '19:00:00';
        
        // Monday
        if ($currentDay == 1 && $currentTime < $drawTime) {
            $nextMonday = date('Y-m-d 19:00:00');
        } else {
            $nextMonday = date('Y-m-d 19:00:00', strtotime('next monday'));
        }
        $db->prepare("INSERT IGNORE INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES ('Monday Lotto', ?, 10.00, 'scheduled')")->execute([$nextMonday]);
        
        // Wednesday
        if ($currentDay == 3 && $currentTime < $drawTime) {
            $nextWednesday = date('Y-m-d 19:00:00');
        } else {
            $nextWednesday = date('Y-m-d 19:00:00', strtotime('next wednesday'));
        }
        $db->prepare("INSERT IGNORE INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES ('Wednesday Lotto', ?, 10.00, 'scheduled')")->execute([$nextWednesday]);
        
        // Friday
        if ($currentDay == 5 && $currentTime < $drawTime) {
            $nextFriday = date('Y-m-d 19:00:00');
        } else {
            $nextFriday = date('Y-m-d 19:00:00', strtotime('next friday'));
        }
        $db->prepare("INSERT IGNORE INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES ('Friday Lotto', ?, 10.00, 'scheduled')")->execute([$nextFriday]);
    }
    
    $query = "SELECT lottery as lottery_type, draw_date, jackpot 
              FROM upcoming_draws 
              WHERE draw_date >= NOW()
              ORDER BY draw_date LIMIT 3";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'draws' => $draws]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>