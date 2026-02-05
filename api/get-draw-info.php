<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$lottery = $_GET['lottery'] ?? '';

if (!$lottery) {
    http_response_code(400);
    echo json_encode(['error' => 'Lottery type required']);
    exit;
}

try {
    // First try to get from past_draw (most recent)
    $query = "SELECT draw_date, jackpot FROM past_draw 
              WHERE lottery = ? 
              ORDER BY draw_date DESC LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$lottery]);
    $draw = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If no past draw, get from upcoming_draw that has passed
    if (!$draw) {
        $query = "SELECT draw_date, jackpot FROM upcoming_draw 
                  WHERE lottery = ? AND draw_date <= NOW() 
                  ORDER BY draw_date DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$lottery]);
        $draw = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($draw) {
        echo json_encode([
            'success' => true,
            'drawDate' => $draw['draw_date'],
            'jackpot' => $draw['jackpot']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No past draw found for this lottery'
        ]);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
