<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

try {
    $user = JWT::authenticate();
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Get detailed winnings with simpler query first
    $detailQuery = "SELECT w.id, w.lottery, w.prize_amount, w.payment_status, 
                    w.created_at, w.paid_at,
                    e.numbers as entry_numbers, e.bonus_numbers as entry_bonus
                    FROM winner w 
                    LEFT JOIN entry e ON w.entry_id = e.id 
                    WHERE w.user_id = ? 
                    ORDER BY w.created_at DESC";
    
    $detailStmt = $db->prepare($detailQuery);
    $detailStmt->execute([$user['id']]);
    $winnings = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total winnings
    $query = "SELECT COALESCE(SUM(prize_amount), 0) as total_winnings 
              FROM winner 
              WHERE user_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'total_winnings' => floatval($result['total_winnings']),
        'winnings' => $winnings
    ]);
    
} catch(Exception $exception) {
    error_log("My winnings error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Failed to fetch winnings', 
        'details' => $exception->getMessage()
    ]);
}
?>
