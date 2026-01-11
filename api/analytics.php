<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $range = $_GET['range'] ?? '30d';
    
    // Calculate date range
    $days = 30;
    switch ($range) {
        case '7d':
            $days = 7;
            break;
        case '30d':
            $days = 30;
            break;
        case '90d':
            $days = 90;
            break;
        case 'custom':
            $days = 30; // Default for custom
            break;
    }
    
    // Handle custom date range
    if ($range === 'custom') {
        $dateFrom = $_GET['dateFrom'] ?? null;
        $dateTo = $_GET['dateTo'] ?? null;
        
        if ($dateFrom && $dateTo) {
            $startDate = $dateFrom . ' 00:00:00';
            $endDate = $dateTo . ' 23:59:59';
        } else {
            $startDate = date('Y-m-d H:i:s', strtotime('-30 days'));
            $endDate = date('Y-m-d H:i:s');
        }
    } else {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $endDate = date('Y-m-d H:i:s');
    }
    
    // Get total plays (entries)
    $stmt = $db->prepare("SELECT COUNT(*) as total_plays FROM entries WHERE created_at >= ? AND created_at <= ?");
    $stmt->execute([$startDate, $endDate]);
    $totalPlays = $stmt->fetch(PDO::FETCH_ASSOC)['total_plays'];
    
    // Calculate total revenue (entries are free, so revenue is 0)
    $totalRevenue = 0;
    
    // Get number of draws in period
    $stmt = $db->prepare("SELECT COUNT(DISTINCT lottery, DATE(draw_date)) as total_draws FROM results WHERE draw_date >= ? AND draw_date <= ?");
    $stmt->execute([$startDate, $endDate]);
    $totalDraws = $stmt->fetch(PDO::FETCH_ASSOC)['total_draws'];
    
    // Calculate average players per draw
    $averagePlayersPerDraw = $totalDraws > 0 ? round($totalPlays / $totalDraws) : 0;
    
    // Get winner count
    $stmt = $db->prepare("SELECT COUNT(*) as total_winners FROM winners WHERE created_at >= ? AND created_at <= ?");
    $stmt->execute([$startDate, $endDate]);
    $totalWinners = $stmt->fetch(PDO::FETCH_ASSOC)['total_winners'];
    
    // Calculate winner rate
    $winnerRate = $totalPlays > 0 ? round(($totalWinners / $totalPlays) * 100, 1) : 0;
    
    echo json_encode([
        'success' => true,
        'totalPlays' => (int)$totalPlays,
        'totalRevenue' => (int)$totalRevenue,
        'averagePlayersPerDraw' => (int)$averagePlayersPerDraw,
        'winnerRate' => (float)$winnerRate,
        'popularNumbers' => [],
        'lotteryPerformance' => [],
        'userEngagement' => [],
        'revenueByMonth' => [],
        'topLotteries' => []
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>