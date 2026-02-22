<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$userId = $_GET['userId'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit;
}

try {
    // Get total entries
    $entriesQuery = "SELECT COUNT(*) as total FROM entry WHERE user_id = ?";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$userId]);
    $totalEntries = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total winnings
    $winningsQuery = "SELECT COALESCE(SUM(prize_amount), 0) as total FROM winner WHERE user_id = ?";
    $stmt = $db->prepare($winningsQuery);
    $stmt->execute([$userId]);
    $totalWinnings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get member since date
    $userQuery = "SELECT created_at FROM user WHERE id = ?";
    $stmt = $db->prepare($userQuery);
    $stmt->execute([$userId]);
    $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $memberSince = $userResult ? $userResult['created_at'] : null;
    
    echo json_encode([
        'success' => true,
        'totalEntries' => (int)$totalEntries,
        'totalWinnings' => number_format((float)$totalWinnings, 2),
        'memberSince' => $memberSince
    ]);
    
} catch(PDOException $exception) {
    error_log("User stats error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch statistics', 'details' => $exception->getMessage()]);
}
?>
