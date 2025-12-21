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
    // Get results from database
    $query = "SELECT * FROM results ORDER BY draw_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format results for frontend
    $formattedResults = [];
    foreach ($results as $result) {
        $formattedResults[] = [
            'id' => $result['id'],
            'lottery' => $result['lottery'],
            'drawDate' => $result['draw_date'],
            'numbers' => json_decode($result['winning_numbers']),
            'bonusNumbers' => json_decode($result['bonus_numbers']),
            'jackpot' => $result['jackpot'],
            'winners' => $result['winners'],
            'status' => $result['status'] ?? 'published',
            'notes' => $result['notes'] ?? '',
            'updatedAt' => $result['created_at']
        ];
    }
    
    echo json_encode($formattedResults);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch results']);
}
?>