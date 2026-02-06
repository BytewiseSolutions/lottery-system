<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Get draws from upcoming_draw table
    $query = "SELECT id, lottery as name, lottery, draw_date as drawDate, draw_date as nextDraw, jackpot, status 
              FROM upcoming_draw 
              WHERE draw_date >= NOW()
              ORDER BY draw_date";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format jackpot for display and ensure all fields are set
    foreach ($draws as &$draw) {
        $draw['id'] = (int)$draw['id'];
        $draw['name'] = (string)($draw['name'] ?: '');
        $draw['lottery'] = (string)($draw['lottery'] ?: '');
        $draw['drawDate'] = (string)($draw['drawDate'] ?: '');
        $draw['nextDraw'] = (string)($draw['nextDraw'] ?: '');
        $draw['status'] = (string)($draw['status'] ?: 'scheduled');
        $draw['jackpot'] = '$' . number_format((float)$draw['jackpot'], 2);
    }
    
    echo json_encode($draws);
    
} catch(PDOException $exception) {
    error_log("Draws error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch draws', 'details' => $exception->getMessage()]);
}
?>
