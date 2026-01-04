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
    
    // Get active lotteries count (3 lotteries: Monday, Wednesday, Friday)
    $activeLotteries = 3;
    
    // Get pending actions (check if status column exists)
    $pendingActions = 0;
    try {
        $pendingQuery = "SELECT COUNT(*) as pending_actions FROM results WHERE status = 'draft'";
        $pendingStmt = $db->prepare($pendingQuery);
        $pendingStmt->execute();
        $pendingActions = $pendingStmt->fetch(PDO::FETCH_ASSOC)['pending_actions'];
    } catch (PDOException $e) {
        // Status column doesn't exist, ignore
    }
    
    // Get total entries count
    $entriesQuery = "SELECT COUNT(*) as total_entries FROM entries";
    $entriesStmt = $db->prepare($entriesQuery);
    $entriesStmt->execute();
    $totalEntries = $entriesStmt->fetch(PDO::FETCH_ASSOC)['total_entries'];
    
    // Calculate pool money (0.01 per entry + base amounts)
    $totalPoolMoney = ($totalEntries * 0.01) + 30; // 3 lotteries x $10 base
    
    $stats = [
        'totalUsers' => (int)$userCount,
        'activeLotteries' => (int)$activeLotteries,
        'pendingActions' => (int)$pendingActions,
        'unreadNotifications' => 0,
        'totalPoolMoney' => (float)$totalPoolMoney,
        'resultsGrowth' => 12,
        'usersGrowth' => -2
    ];
    
    echo json_encode($stats);
    
} catch(PDOException $exception) {
    error_log("Dashboard stats error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch dashboard stats', 'details' => $exception->getMessage()]);
}
?>