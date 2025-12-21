<?php
require_once 'config/database.php';
require_once 'config/cors.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$range = $_GET['range'] ?? '7d';

try {
    // Get date range
    $days = match($range) {
        '7d' => 7,
        '30d' => 30,
        '90d' => 90,
        default => 7
    };
    
    $startDate = date('Y-m-d', strtotime("-$days days"));
    
    // Chart data for results over time
    $stmt = $db->prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM results 
        WHERE created_at >= ? 
        GROUP BY DATE(created_at) 
        ORDER BY date
    ");
    $stmt->execute([$startDate]);
    $resultsChart = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // User registrations over time
    $stmt = $db->prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM users 
        WHERE created_at >= ? 
        GROUP BY DATE(created_at) 
        ORDER BY date
    ");
    $stmt->execute([$startDate]);
    $usersChart = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top lotteries by entries
    $stmt = $db->prepare("
        SELECT lottery, COUNT(*) as entries 
        FROM entries 
        WHERE created_at >= ? 
        GROUP BY lottery 
        ORDER BY entries DESC 
        LIMIT 5
    ");
    $stmt->execute([$startDate]);
    $topLotteries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Summary stats
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM users WHERE created_at >= ?");
    $stmt->execute([$startDate]);
    $newUsers = $stmt->fetchColumn();
    
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM entries WHERE created_at >= ?");
    $stmt->execute([$startDate]);
    $totalEntries = $stmt->fetchColumn();
    
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM results WHERE created_at >= ?");
    $stmt->execute([$startDate]);
    $totalResults = $stmt->fetchColumn();
    
    echo json_encode([
        'resultsChart' => $resultsChart,
        'usersChart' => $usersChart,
        'topLotteries' => $topLotteries,
        'summary' => [
            'newUsers' => $newUsers,
            'totalEntries' => $totalEntries,
            'totalResults' => $totalResults
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load analytics data']);
}
?>