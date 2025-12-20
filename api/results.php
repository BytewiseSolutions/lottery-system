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
    // Return empty results for now (no draws have happened yet)
    $results = [];
    
    // You can add actual results here when draws happen
    // Example structure:
    // $results = [
    //     [
    //         'id' => 1,
    //         'lottery' => 'Monday Lotto',
    //         'drawDate' => '2024-12-16',
    //         'winningNumbers' => [5, 12, 23, 34, 45],
    //         'bonusNumbers' => [7, 18],
    //         'jackpot' => '$15.50',
    //         'winners' => 0
    //     ]
    // ];
    
    echo json_encode($results);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch results']);
}
?>