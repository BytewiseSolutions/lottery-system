<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/cors.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();

echo json_encode([
    'method' => $method,
    'received_data' => $data,
    'headers' => $headers,
    'raw_input' => file_get_contents('php://input')
]);
?>