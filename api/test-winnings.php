<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/cors.php';

echo json_encode([
    'success' => true,
    'message' => 'Test endpoint working',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>