<?php
require_once 'config/database.php';

function getNextDrawDate($dayName) {
    $days = ['monday' => 1, 'wednesday' => 3, 'friday' => 5];
    $now = new DateTime();
    $targetDay = $days[$dayName];
    $currentDay = $now->format('N');
    $currentHour = $now->format('H');
    
    $daysUntilNext = $targetDay - $currentDay;
    
    if ($daysUntilNext === 0 && $currentHour >= 20) {
        $daysUntilNext = 7;
    } elseif ($daysUntilNext < 0) {
        $daysUntilNext += 7;
    } elseif ($daysUntilNext === 0) {
        $daysUntilNext = 0;
    }
    
    $nextDate = clone $now;
    if ($daysUntilNext > 0) {
        $nextDate->add(new DateInterval('P' . $daysUntilNext . 'D'));
    }
    $nextDate->setTime(20, 0, 0);
    
    return $nextDate->format('Y-m-d H:i:s');
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "Database connection failed\n";
    exit;
}

try {
    $lotteries = [
        ['name' => 'Monday Lotto', 'day' => 'monday', 'jackpot' => '$10.00'],
        ['name' => 'Wednesday Lotto', 'day' => 'wednesday', 'jackpot' => '$10.00'],
        ['name' => 'Friday Lotto', 'day' => 'friday', 'jackpot' => '$10.00']
    ];
    
    foreach ($lotteries as $lottery) {
        $nextDrawDate = getNextDrawDate($lottery['day']);
        
        // Check if this draw already exists
        $checkQuery = "SELECT id FROM upcoming_draws WHERE lottery = ? AND draw_date = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([$lottery['name'], $nextDrawDate]);
        
        if ($checkStmt->rowCount() === 0) {
            // Insert new upcoming draw
            $insertQuery = "INSERT INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES (?, ?, ?, 'scheduled')";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute([$lottery['name'], $nextDrawDate, $lottery['jackpot']]);
            echo "Added {$lottery['name']} draw for {$nextDrawDate}\n";
        }
    }
    
    // Move past draws to past_draws table instead of deleting
    $moveQuery = "INSERT INTO past_draws (lottery, draw_date, jackpot, status) 
                  SELECT lottery, draw_date, jackpot, 'completed' 
                  FROM upcoming_draws 
                  WHERE draw_date < NOW()";
    $moveStmt = $db->prepare($moveQuery);
    $moveStmt->execute();
    
    // Now remove them from upcoming_draws
    $deleteQuery = "DELETE FROM upcoming_draws WHERE draw_date < NOW()";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->execute();
    
    if ($moveStmt->rowCount() > 0) {
        echo "Moved {$moveStmt->rowCount()} past draws to history\n";
    }
    
    echo "Upcoming draws updated successfully\n";
    
} catch(PDOException $exception) {
    echo "Error: " . $exception->getMessage() . "\n";
}
?>