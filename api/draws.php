<?php
require_once 'config/cors.php';
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
    }
    
    $nextDate = clone $now;
    $nextDate->add(new DateInterval('P' . $daysUntilNext . 'D'));
    $nextDate->setTime(20, 0, 0);
    
    return $nextDate->format('c');
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Get pool data for current draws
    $query = "SELECT lottery, COUNT(*) * 0.001 as pool_amount FROM entries GROUP BY lottery";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $poolData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize balances
    $balances = ['Mon Lotto' => 0, 'Wed Lotto' => 0, 'Fri Lotto' => 0];
    
    foreach ($poolData as $row) {
        $balances[$row['lottery']] = floatval($row['pool_amount']);
    }
    
    $draws = [];
    $lotteries = ['Mon Lotto', 'Wed Lotto', 'Fri Lotto'];
    $days = ['monday', 'wednesday', 'friday'];
    
    // This week
    foreach ($lotteries as $index => $lottery) {
        $nextDraw = getNextDrawDate($days[$index]);
        
        $draws[] = [
            'id' => $index + 1,
            'name' => $lottery,
            'jackpot' => '$' . number_format($balances[$lottery], 3),
            'nextDraw' => $nextDraw
        ];
    }
    

    
    echo json_encode($draws);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch draws']);
}
?>