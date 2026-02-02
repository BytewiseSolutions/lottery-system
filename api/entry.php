<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

date_default_timezone_set('Africa/Johannesburg');

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

try {
    // If admin, return all entries with user names. Otherwise, return user's entries
    if (isset($user['role']) && $user['role'] === 'admin') {
        $query = "SELECT e.*, u.full_name as user_name 
                  FROM entry e 
                  LEFT JOIN user u ON e.user_id = u.id 
                  ORDER BY e.created_at DESC";
        $stmt = $db->query($query);
    } else {
        $query = "SELECT e.*, u.full_name as user_name 
                  FROM entry e 
                  LEFT JOIN user u ON e.user_id = u.id 
                  WHERE e.user_id = ? 
                  ORDER BY e.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$user['id']]);
    }
    
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($entries);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch entries']);
}
?>