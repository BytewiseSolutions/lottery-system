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
    // Get past draws with results from database
    $query = "SELECT r.id, r.lottery, r.draw_date, r.winning_numbers, r.bonus_numbers, r.jackpot, r.winners
              FROM result r 
              WHERE r.status = 'published' AND r.draw_date < NOW()
              ORDER BY r.draw_date DESC LIMIT 50";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($draws);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch past draws']);
}
?>