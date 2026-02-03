<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

try {
    // Get total winnings
    $query = "SELECT COALESCE(SUM(prize_amount), 0) as total_winnings 
              FROM winner 
              WHERE user_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get detailed winnings
    $detailQuery = "SELECT w.*, 
                    COALESCE(r.lottery, w.lottery) as lottery, 
                    COALESCE(r.draw_date, w.draw_date) as drawDate, 
                    e.numbers, 
                    e.bonus_numbers 
                    FROM winner w 
                    LEFT JOIN result r ON w.result_id = r.id 
                    LEFT JOIN entry e ON w.entry_id = e.id 
                    WHERE w.user_id = ? 
                    ORDER BY w.created_at DESC";
    
    $detailStmt = $db->prepare($detailQuery);
    $detailStmt->execute([$user['id']]);
    $winnings = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'total_winnings' => floatval($result['total_winnings']),
        'winnings' => $winnings
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch winnings']);
}
?>
