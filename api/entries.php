<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Get all entries (for admin) or user-specific entries
    $user = null;
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        try {
            $user = JWT::authenticate();
        } catch (Exception $e) {
            // Not authenticated, return empty array
            echo json_encode([]);
            exit;
        }
    }
    
    if ($user) {
        // Return entries for authenticated user
        $query = "SELECT * FROM entries WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$user['id']]);
    } else {
        // Return empty array for non-authenticated users
        echo json_encode([]);
        exit;
    }
    
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($entries);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch entries']);
}
?>