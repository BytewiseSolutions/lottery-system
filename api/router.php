<?php
// Router for PHP built-in server to handle /api/ prefix

// Handle CORS for all requests FIRST
$allowedOrigins = [
    'http://localhost:4200',  
    'http://localhost:3000',
    'http://127.0.0.1:4200'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins) || strpos($origin, 'localhost') !== false) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: http://localhost:4200");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Now include the original CORS config
require_once 'config/cors.php';

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

if (strpos($path, '/api/') === 0) {
    $path = substr($path, 5); 
}

// Remove leading slash if present
if (strpos($path, '/') === 0) {
    $path = substr($path, 1);
}

$urlMappings = [
    'draws' => 'draw.php',
    'user-stats' => 'user-stats.php',
    'upload-profile-picture' => 'upload-profile-picture.php',
    'get-file' => 'get-file.php',
    'my-winnings' => 'my-winnings.php',
    'results' => 'result.php',
    'login' => 'login.php',
    'register' => 'register.php',
    'profile' => 'profile.php',
    'update-profile' => 'update-profile.php',
    'change-password' => 'change-password.php',
    'send-reset-code' => 'send-reset-code.php',
    'reset-password' => 'reset-password.php',
    'entries' => 'entry.php',
    'play' => 'play.php',
    'vote' => 'vote.php',
    'notifications' => 'user-notifications.php',
    'mark-notification-read' => 'mark-notification-read.php',
    'past-draws' => 'past-draws.php',
    'upcoming-draws' => 'upcoming-draw.php',
    'winners' => 'winner.php',
    'contact' => 'contact.php',
    'analytics' => 'analytics.php',
    'stats' => 'stat.php',
    'dashboard-stats' => 'dashboard-stats.php',
    'leading-numbers' => 'leading-numbers.php',
    'voting-history' => 'voting-history.php',
    'voting-countdown' => 'voting-countdown.php',
    'current-voting-draw' => 'current-voting-draw.php',
    'quick-pick' => 'quick-pick.php',
    'test-connection' => 'test-connection.php',
    'database-check' => 'database-check.php'
];

if (empty($path) || $path === '/') {
    header('Content-Type: application/json');
    echo json_encode([
        'message' => 'Lottery System API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
    exit;
}

// Remove query string from path for mapping
$pathWithoutQuery = strtok($path, '?');

// Check if we have a mapping for this URL
if (isset($urlMappings[$pathWithoutQuery])) {
    $file = __DIR__ . '/' . $urlMappings[$pathWithoutQuery];
} else {
    // Check if the requested file exists as-is
    $file = __DIR__ . '/' . $pathWithoutQuery;
    // Also try with .php extension
    if (!file_exists($file) && substr($pathWithoutQuery, -4) !== '.php') {
        $file = __DIR__ . '/' . $pathWithoutQuery . '.php';
    }
}

if (file_exists($file) && is_file($file)) {
    // Serve the PHP file
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        include $file;
    } else {
        // Serve static files
        $mimeType = mime_content_type($file);
        header('Content-Type: ' . $mimeType);
        readfile($file);
    }
} else {
    // File not found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Endpoint not found']);
}
?>
