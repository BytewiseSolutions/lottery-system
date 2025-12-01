<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Get total entries
    $query = "SELECT COUNT(*) as total FROM entries";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $totalEntries = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total payouts
    $query = "SELECT SUM(total_pool_money) as total FROM results";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $totalPayouts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
    
    // Get winners last month (assuming winners table exists)
    $query = "SELECT COUNT(*) as winners FROM winners 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $winnersLastMonth = $stmt->fetch(PDO::FETCH_ASSOC)['winners'] ?: 0;
    
    $stats = [
        'winnersLastMonth' => $winnersLastMonth,
        'totalEntries' => $totalEntries,
        'totalPayouts' => $totalPayouts
    ];
    
    echo json_encode($stats);
    
} catch(PDOException $exception) {
    echo json_encode([
        'winnersLastMonth' => 0,
        'totalEntries' => 0,
        'totalPayouts' => 0
    ]);
}
?>