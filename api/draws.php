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
    
    // If it's the same day but after 8 PM, move to next week
    if ($daysUntilNext === 0 && $currentHour >= 20) {
        $daysUntilNext = 7;
    } 
    // If the target day has passed this week, move to next week
    elseif ($daysUntilNext < 0) {
        $daysUntilNext += 7;
    }
    // If it's the same day and before 8 PM, it's today
    elseif ($daysUntilNext === 0) {
        $daysUntilNext = 0;
    }
    
    $nextDate = clone $now;
    if ($daysUntilNext > 0) {
        $nextDate->add(new DateInterval('P' . $daysUntilNext . 'D'));
    }
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
    // Initialize balances with $10 base amount
    $balances = ['Monday Lotto' => 10, 'Wednesday Lotto' => 10, 'Friday Lotto' => 10];
    
    // Get pool data only for next draw dates
    $lotteries = ['Monday Lotto', 'Wednesday Lotto', 'Friday Lotto'];
    $days = ['monday', 'wednesday', 'friday'];
    
    foreach ($lotteries as $index => $lottery) {
        $nextDrawDate = getNextDrawDate($days[$index]);
        $drawDateOnly = date('Y-m-d', strtotime($nextDrawDate));
        
        // Count entries for this specific draw date
        $query = "SELECT COUNT(*) * 0.01 as pool_amount FROM entries WHERE (lottery = ? OR lottery = ?) AND draw_date = ?";
        $stmt = $db->prepare($query);
        $oldName = str_replace(' Lotto', ' Lotto', $lottery);
        if ($lottery === 'Monday Lotto') $oldName = 'Mon Lotto';
        if ($lottery === 'Wednesday Lotto') $oldName = 'Wed Lotto';
        if ($lottery === 'Friday Lotto') $oldName = 'Fri Lotto';
        
        $stmt->execute([$lottery, $oldName, $drawDateOnly]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['pool_amount']) {
            $balances[$lottery] += floatval($result['pool_amount']);
        }
        
        // Ensure minimum $10 base amount
        if ($balances[$lottery] < 10) {
            $balances[$lottery] = 10;
        }
    }
    
    $draws = [];
    $lotteries = ['Monday Lotto', 'Wednesday Lotto', 'Friday Lotto'];
    $days = ['monday', 'wednesday', 'friday'];
    
    // Generate draws with next draw dates
    foreach ($lotteries as $index => $lottery) {
        $nextDraw = getNextDrawDate($days[$index]);
        
        $draws[] = [
            'id' => $index + 1,
            'name' => $lottery,
            'jackpot' => '$' . number_format($balances[$lottery], 2),
            'nextDraw' => $nextDraw,
            'sortDate' => new DateTime($nextDraw)
        ];
    }
    
    // Sort by next draw date (soonest first)
    usort($draws, function($a, $b) {
        return $a['sortDate'] <=> $b['sortDate'];
    });
    
    // Remove sortDate before sending response
    foreach ($draws as &$draw) {
        unset($draw['sortDate']);
    }
    
    echo json_encode($draws);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch draws']);
}
?>