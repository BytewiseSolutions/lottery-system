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
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM user");
    $stmt->execute();
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get active users (logged in last 30 days)
    $stmt = $db->prepare("SELECT COUNT(*) as active FROM user WHERE is_active = 1");
    $stmt->execute();
    $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    // Get total plays from entry table
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM entry");
    $stmt->execute();
    $totalPlays = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Revenue is always 0 (free lottery)
    $totalRevenue = 0;
    
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