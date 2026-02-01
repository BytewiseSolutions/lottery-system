<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT COALESCE(SUM(prize_amount), 0) as total_winnings 
              FROM winner 
              WHERE user_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'total_winnings' => floatval($result['total_winnings'])
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch winnings']);
}
?>
