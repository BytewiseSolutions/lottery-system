<?php
// Include CORS headers for all requests
require_once 'config/cors.php';

// Router for PHP built-in server
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove query string and leading slash
$path = trim($path, '/');

// Route API requests
switch ($path) {
    case 'api/health':
        require 'health.php';
        break;
    case 'api/register':
        require 'register.php';
        break;
    case 'api/login':
        require 'login.php';
        break;
    case 'api/verify-otp':
        require 'verify-otp.php';
        break;
    case 'api/resend-otp':
        require 'resend-otp.php';
        break;
    case 'api/draws':
        require 'draws.php';
        break;
    case 'api/play':
        require 'play.php';
        break;
    case 'api/results':
        require 'results.php';
        break;
    case 'api/entries':
        require 'entries.php';
        break;
    case 'api/pool':
        require 'pool.php';
        break;
    case 'api/stats':
        require 'stats.php';
        break;
    case 'api/dashboard-stats':
        require 'dashboard-stats.php';
        break;
    case 'api/upcoming-draws':
        require 'upcoming-draws.php';
        break;
    case 'api/admin-upload-result':
        require 'admin-upload-result-enhanced.php';
        break;
    case 'api/admin-delete-result':
        require 'admin-delete-result.php';
        break;
    case 'api/update-result-status':
        require 'update-result-status.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
?>