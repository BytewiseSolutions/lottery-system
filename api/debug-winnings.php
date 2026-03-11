<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Step 1: Test CORS
    require_once 'config/cors.php';
    $debug = ['step1' => 'CORS loaded'];
    
    // Step 2: Test Database
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    $debug['step2'] = $db ? 'Database connected' : 'Database failed';
    
    // Step 3: Test JWT (without authentication)
    require_once 'config/jwt.php';
    $debug['step3'] = 'JWT class loaded';
    
    // Step 4: Check headers
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? 'none';
    $debug['step4'] = 'Auth header: ' . $authHeader;
    
    // Return debug info
    echo json_encode([
        'success' => true,
        'debug' => $debug,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
?>