<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get current time in South Africa timezone
    $timezone = new DateTimeZone('Africa/Johannesburg');
    $now = new DateTime('now', $timezone);
    
    // Get the next upcoming draw
    $stmt = $db->prepare("
        SELECT id, lottery, draw_date, jackpot, status 
        FROM upcoming_draw 
        WHERE status = 'scheduled' 
        ORDER BY draw_date ASC
        LIMIT 1
    ");
    $stmt->execute();
    $draw = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$draw) {
        echo json_encode([
            'success' => false,
            'error' => 'No upcoming draws found'
        ]);
        exit;
    }
    
    // Get the draw date and set voting close time to 19:59 on that day
    $drawDateTime = new DateTime($draw['draw_date'], $timezone);
    $votingCloseTime = clone $drawDateTime;
    $votingCloseTime->setTime(19, 59, 0); // 7:59 PM on draw day
    
    // Calculate time remaining
    $interval = $now->diff($votingCloseTime);
    $totalSeconds = 0;
    
    if ($now < $votingCloseTime) {
        $totalSeconds = ($interval->days * 24 * 3600) + 
                       ($interval->h * 3600) + 
                       ($interval->i * 60) + 
                       $interval->s;
    }
    
    // Format countdown
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;
    
    $response = [
        'success' => true,
        'countdown' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
        'totalSeconds' => $totalSeconds,
        'votingCloseTime' => $votingCloseTime->format('Y-m-d H:i:s'),
        'currentTime' => $now->format('Y-m-d H:i:s'),
        'drawDate' => $drawDateTime->format('Y-m-d H:i:s'),
        'isVotingOpen' => $totalSeconds > 0
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>