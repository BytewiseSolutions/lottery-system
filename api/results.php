<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT lottery, winning_numbers, bonus_numbers, draw_date, total_pool_money 
              FROM results 
              ORDER BY draw_date DESC 
              LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedResults = [];
    foreach ($results as $result) {
        $formattedResults[] = [
            'game' => $result['lottery'],
            'numbers' => json_decode($result['winning_numbers']),
            'bonusNumbers' => json_decode($result['bonus_numbers']),
            'date' => $result['draw_date'],
            'poolMoney' => '$' . $result['total_pool_money']
        ];
    }
    
    echo json_encode($formattedResults);
    
} catch(PDOException $exception) {
    echo json_encode([]);
}
?>