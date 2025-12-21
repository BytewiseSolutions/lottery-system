<?php
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
    // Get past draws from database
    $query = "SELECT * FROM past_draws ORDER BY draw_date DESC LIMIT 50";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format draws for frontend
    $formattedDraws = [];
    foreach ($draws as $draw) {
        $formattedDraws[] = [
            'id' => $draw['id'],
            'lottery' => $draw['lottery'],
            'drawDate' => $draw['draw_date'],
            'jackpot' => $draw['jackpot'],
            'status' => $draw['status'],
            'movedAt' => $draw['moved_at']
        ];
    }
    
    echo json_encode($formattedDraws);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch past draws']);
}
?>