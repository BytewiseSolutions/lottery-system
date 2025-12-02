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
    // Get pool data
    $query = "SELECT lottery, draw_date, COUNT(*) * 0.001 as pool_amount FROM entries GROUP BY lottery, draw_date";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $specificPools = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize balances
    $thisWeekBalances = ['Mon Lotto' => 0, 'Wed Lotto' => 0, 'Fri Lotto' => 0];
    $nextWeekBalances = ['Mon Lotto' => 0, 'Wed Lotto' => 0, 'Fri Lotto' => 0];
    
    foreach ($specificPools as $row) {
        $dateStr = date('Y-m-d', strtotime($row['draw_date']));
        $balance = floatval($row['pool_amount']);
        
        // This week entries
        if (in_array($dateStr, ['2025-11-29', '2025-11-30', '2025-12-01', '2025-12-04', '2025-12-05'])) {
            $thisWeekBalances[$row['lottery']] += $balance;
        }
        // Next week entries
        elseif (in_array($dateStr, ['2025-12-07', '2025-12-08', '2025-12-09', '2025-12-10', '2025-12-11', '2025-12-12'])) {
            $nextWeekBalances[$row['lottery']] += $balance;
        }
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
            'jackpot' => '$' . number_format($thisWeekBalances[$lottery], 3),
            'nextDraw' => $nextDraw
        ];
    }
    
    // Next week
    foreach ($lotteries as $index => $lottery) {
        $thisWeekDraw = new DateTime(getNextDrawDate($days[$index]));
        $nextWeekDraw = clone $thisWeekDraw;
        $nextWeekDraw->add(new DateInterval('P7D'));
        
        $draws[] = [
            'id' => $index + 4,
            'name' => $lottery,
            'jackpot' => '$' . number_format($nextWeekBalances[$lottery], 3),
            'nextDraw' => $nextWeekDraw->format('c')
        ];
    }
    
    echo json_encode($draws);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch draws']);
}
?>