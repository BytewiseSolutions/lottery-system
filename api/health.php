<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$response = [
    'status' => 'API is running',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => $_ENV['APP_ENV'] ?? 'development'
];

// Test database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $response['database'] = 'connected';
        
        // Test a simple query
        $stmt = $db->query("SELECT 1 as test");
        if ($stmt) {
            $response['database_query'] = 'working';
        }
    } else {
        $response['database'] = 'failed';
    }
} catch (Exception $e) {
    $response['database'] = 'error';
    $response['database_error'] = $e->getMessage();
}

echo json_encode($response);
?>