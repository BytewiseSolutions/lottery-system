<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get total users
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get active users (logged in last 30 days)
    $stmt = $db->prepare("SELECT COUNT(*) as active FROM users WHERE is_active = 1");
    $stmt->execute();
    $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    // Get total plays (assuming plays table exists, otherwise return 0)
    $totalPlays = 0;
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM plays");
        $stmt->execute();
        $totalPlays = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch(Exception $e) {
        // Table doesn't exist yet
    }
    
    // Get total revenue (assuming plays table with amount column)
    $totalRevenue = 0;
    try {
        $stmt = $db->prepare("SELECT SUM(amount) as revenue FROM plays");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRevenue = $result['revenue'] ?? 0;
    } catch(Exception $e) {
        // Table doesn't exist yet
    }
    
    $stats = [
        'totalUsers' => (int)$totalUsers,
        'activeUsers' => (int)$activeUsers,
        'totalPlays' => (int)$totalPlays,
        'totalRevenue' => (float)$totalRevenue,
        'conversionRate' => $totalUsers > 0 ? round(($totalPlays / $totalUsers) * 100, 2) : 0
    ];
    
    echo json_encode(['success' => true, 'stats' => $stats]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>