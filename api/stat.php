<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Get total users
    $query = "SELECT COUNT(*) as total FROM user";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUsers = $result ? (int)$result['total'] : 0;
    
    // Get total entries
    $query = "SELECT COUNT(*) as total FROM entry";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalEntries = (int)$result['total'];
    
    // Get total payouts
    $query = "SELECT SUM(jackpot) as total FROM result";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $totalPayouts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
    
    // Get winners last month (assuming winners table exists)
    $query = "SELECT COUNT(*) as winners FROM winner 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $winnersLastMonth = $stmt->fetch(PDO::FETCH_ASSOC)['winners'] ?: 0;
    
    $stats = [
        'totalUsers' => $totalUsers,
        'winnersLastMonth' => $winnersLastMonth,
        'totalEntries' => $totalEntries,
        'totalPayouts' => $totalPayouts
    ];
    
    echo json_encode($stats);
    
} catch(PDOException $exception) {
    error_log('Stat.php error: ' . $exception->getMessage());
    echo json_encode([
        'totalUsers' => 0,
        'winnersLastMonth' => 0,
        'totalEntries' => 0,
        'totalPayouts' => 0,
        'error' => $exception->getMessage()
    ]);
}
?>