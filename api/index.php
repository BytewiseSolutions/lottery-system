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
        require 'draw.php';
        break;
    case 'api/play':
        require 'play.php';
        break;
    case 'api/results':
        require 'result.php';
        break;
    case 'api/entries':
        require 'entry.php';
        break;
    case 'api/pool':
        require 'pool.php';
        break;
    case 'api/stats':
        require 'stat.php';
        break;
    case 'api/dashboard-stats':
        require 'stat.php';
        break;
    case 'api/upcoming-draws':
        require 'upcoming-draw.php';
        break;
    case 'api/admin-upload-result':
        require 'upload-result.php';
        break;
    case 'api/admin-delete-result':
        require 'delete-result.php';
        break;
    case 'api/update-result-status':
        require 'result.php';
        break;
    case 'api/analytics':
        require 'analytics.php';
        break;
    case 'api/users':
        require 'user.php';
        break;
    case 'api/winners':
        require 'winner.php';
        break;
    case 'api/send-notification':
        require 'notification.php';
        break;
    case 'api/mark-paid':
        require 'mark-paid.php';
        break;
    case 'api/notifications':
        require 'notification.php';
        break;
    case 'api/user-notifications':
        require 'user-notifications.php';
        break;
    case 'api/mark-notification-read':
        require 'mark-notification-read.php';
        break;
    case 'api/my-winnings':
        require 'my-winnings.php';
        break;
    case 'api/get-draw-info':
        require 'get-draw-info.php';
        break;
    case 'api/user-stats':
        require 'user-stats.php';
        break;
    case 'api/update-profile':
        require 'update-profile.php';
        break;
    case 'api/delete-account':
        require 'delete-account.php';
        break;
    case 'api/change-password':
        require 'change-password.php';
        break;
    case 'api/notification-preferences':
        require 'notification-preferences.php';
        break;
    case 'api/activity-logs':
        require 'activity-logs.php';
        break;
    case 'api/contact':
        require 'contact.php';
        break;
    case 'api/contact-messages':
        require 'contact-messages.php';
        break;
    case 'api/vote':
        require 'vote.php';
        break;
    case 'api/leading-numbers':
        require 'leading-numbers.php';
        break;
    case 'api/voting-history':
        require 'voting-history.php';
        break;
    case 'api/admin-vote':
        require 'admin-vote.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
?>