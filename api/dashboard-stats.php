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
    // Get total users count
    $userQuery = "SELECT COUNT(*) as total_users FROM users";
    $userStmt = $db->prepare($userQuery);
    $userStmt->execute();
    $userCount = $userStmt->fetch(PDO::FETCH_ASSOC)['total_users'];
    
    // Get active lotteries count
    $lotteryQuery = "SELECT COUNT(DISTINCT lottery) as active_lotteries FROM results WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $lotteryStmt = $db->prepare($lotteryQuery);
    $lotteryStmt->execute();
    $activeLotteries = $lotteryStmt->fetch(PDO::FETCH_ASSOC)['active_lotteries'];
    
    // Get pending actions (draft results)
    $pendingQuery = "SELECT COUNT(*) as pending_actions FROM results WHERE status = 'draft'";
    $pendingStmt = $db->prepare($pendingQuery);
    $pendingStmt->execute();
    $pendingActions = $pendingStmt->fetch(PDO::FETCH_ASSOC)['pending_actions'];
    
    $stats = [
        'totalUsers' => (int)$userCount,
        'activeLotteries' => (int)$activeLotteries,
        'pendingActions' => (int)$pendingActions
    ];
    
    echo json_encode($stats);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch dashboard stats']);
}
?>