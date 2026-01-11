<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get upcoming draws (next 7 days)
    $query = "SELECT 
        'monday' as lottery_type, 
        DATE_ADD(CURDATE(), INTERVAL (7 - WEEKDAY(CURDATE())) DAY) as draw_date,
        10.00 as jackpot
    UNION ALL
    SELECT 
        'wednesday' as lottery_type,
        DATE_ADD(CURDATE(), INTERVAL (9 - WEEKDAY(CURDATE())) DAY) as draw_date,
        10.00 as jackpot
    UNION ALL
    SELECT 
        'friday' as lottery_type,
        DATE_ADD(CURDATE(), INTERVAL (11 - WEEKDAY(CURDATE())) DAY) as draw_date,
        10.00 as jackpot
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