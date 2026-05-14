<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

// CORS must be set before anything else, even if bootstrap fails
$allowedOrigins = [
    'https://www.totalfreelotto.com',
    'https://totalfreelotto.com',
    'http://localhost:4200',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    require_once __DIR__ . '/../config/bootstrap.php';
    
    CorsMiddleware::handle();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Clean the URI for routing
    $uri = str_replace('/api', '', $uri);
    $uri = trim($uri, '/');
    
    $routes = explode('/', $uri);
    $module = $routes[0] ?? '';
    $action = $routes[1] ?? '';
    
    if (empty($module)) {
        Response::json(true, 'Lottery System API v1.0', [
            'version' => '1.0.0',
            'status' => 'running',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    switch ($module) {
        case 'auth':
            handleAuthRoutes($method, $action);
            break;
            
        case 'user':
            handleUserRoutes($method, $action);
            break;
            
        case 'vote':
            handleVoteRoutes($method, $action);
            break;

        case 'entry':
            handleEntryRoutes($method, $action);
            break;
            
        case 'draw':
            handleDrawRoutes($method, $action);
            break;
            
        case 'result':
            handleResultRoutes($method, $action);
            break;
            
        case 'winner':
            handleWinnerRoutes($method, $action);
            break;
            
        case 'payment':
            handlePaymentRoutes($method, $action);
            break;
            
        case 'notification':
            handleNotificationRoutes($method, $action);
            break;

        case 'contact':
            handleContactRoutes($method, $action);
            break;
            
        case 'file':
            handleFileRoutes($method, $action);
            break;
            
        case 'analytics':
            handleAnalyticsRoutes($method, $action);
            break;
            
        case 'audit':
            handleAuditRoutes($method, $action);
            break;
            
        case 'settings':
            handleSettingsRoutes($method, $action);
            break;
            
        default:
            Response::json(false, 'Route not found', null, 404);
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    
    $statusCode = $e->getCode() ?: 500;
    Response::json(false, 'Internal server error', null, $statusCode);
}

function handleAuthRoutes($method, $action) {
    $controller = new AuthController();

    switch ($action) {

        case 'login':
            if ($method === 'POST') {
                $controller->login();
            }
            break;

        case 'register':
            if ($method === 'POST') {
                $controller->register();
            }
            break;

        case 'logout':
            if ($method === 'POST') {
                $controller->logout();
            }
            break;
            
        case 'forgot-password':
            if ($method === 'POST') {
                $controller->forgotPassword();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'reset-password':
            if ($method === 'POST') {
                $controller->resetPassword();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        default:
            Response::json(false, 'Auth endpoint not found', null, 404);
    }
}

function handleContactRoutes($method, $action) {
    $controller = new ContactController();

    switch ($action) {
        case '':
        case 'submit':
            if ($method === 'POST') {
                $controller->submit();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        default:
            Response::json(false, 'Contact endpoint not found', null, 404);
    }
}

function handleUserRoutes($method, $action) {
    $controller = new UserController();
    
    switch ($action) {
        case 'register':
            if ($method === 'POST') {
                $controller->register();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'profile':
            if ($method === 'GET') {
                $controller->getCurrentProfile();
            } elseif ($method === 'PUT') {
                $controller->updateProfile();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'stats':
            if ($method === 'GET') {
                $controller->getCurrentStats();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                $controller->deleteCurrentAccount();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case '':  
        case 'list':
            if ($method === 'GET') {
                $controller->getUsers();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'details':
            if ($method === 'GET') {
                $controller->getUserDetails();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'create':
            if ($method === 'POST') {
                $controller->createUser();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'update':
            if ($method === 'PUT') {
                $controller->updateUser();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'status':
            if ($method === 'POST') {
                $controller->updateUserStatus();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'password':
            if ($method === 'POST') {
                $controller->resetUserPassword();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'change-password':
            if ($method === 'POST') {
                $controller->changeCurrentPassword();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'User endpoint not found', null, 404);
    }
}

function handleVoteRoutes($method, $action) {
    $controller = new VoteController();
    
    switch ($action) {
        case 'submit':
            if ($method === 'POST') {
                $controller->submitVote();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'history':
            if ($method === 'GET') {
                $controller->getVoteHistory();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'quick-pick':
            if ($method === 'POST') {
                $controller->getQuickPick();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'leading':
            if ($method === 'GET') {
                $controller->getLeadingNumbers();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'highest-vote':
            if ($method === 'GET') {
                $controller->getHighestVoteForDraw();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'list':
            if ($method === 'GET') {
                $controller->getAdminVotes();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'details':
            if ($method === 'GET') {
                $controller->getAdminVoteDetails();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'create':
            if ($method === 'POST') {
                $controller->createAdminVote();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'update':
            if ($method === 'PUT') {
                $controller->updateAdminVote();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'delete':
            if ($method === 'DELETE') {
                $controller->deleteAdminVote();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Vote endpoint not found', null, 404);
    }
}

function handleEntryRoutes($method, $action) {
    $controller = new EntryController();

    switch ($action) {
        case 'submit':
            if ($method === 'POST') {
                $controller->submitEntry();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'history':
            if ($method === 'GET') {
                $controller->getEntryHistory();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'draw':
            if ($method === 'GET') {
                $controller->getEntriesByDraw();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'list':
            if ($method === 'GET') {
                $controller->getAllEntries();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'details':
            if ($method === 'GET') {
                $controller->getEntryDetails();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        default:
            Response::json(false, 'Entry endpoint not found', null, 404);
    }
}

function handleDrawRoutes($method, $action) {
    $controller = new DrawController();
    
    switch ($action) {
        case 'current':
            if ($method === 'GET') {
                $controller->getCurrentDraw();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'upcoming':
            if ($method === 'GET') {
                $controller->getUpcomingDraws();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'past':
            if ($method === 'GET') {
                $controller->getPastDrawsWithoutResults();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Draw endpoint not found', null, 404);
    }
}

function handleResultRoutes($method, $action) {
    $controller = new ResultController();
    
    switch ($action) {
        case 'latest':
            if ($method === 'GET') {
                $controller->getLatestResults();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'list':
            if ($method === 'GET') {
                $controller->getResults();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'details':
            if ($method === 'GET') {
                $controller->getResultById();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'create':
            if ($method === 'POST') {
                $controller->createResult();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'auto-publish':
            if ($method === 'POST') {
                $controller->autoPublishResults();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'update':
            if ($method === 'PUT') {
                $controller->updateResult();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Result endpoint not found', null, 404);
    }
}

function handleWinnerRoutes($method, $action) {
    $controller = new WinnerController();
    
    switch ($action) {
        case 'list':
            if ($method === 'GET') {
                $controller->getWinners();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'my':
            if ($method === 'GET') {
                $controller->getCurrentUserWinnings();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'claim':
            if ($method === 'POST') {
                $controller->markClaimed();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Winner endpoint not found', null, 404);
    }
}

function handlePaymentRoutes($method, $action) {
    $controller = new PaymentController();
    
    switch ($action) {
        case 'process':
            if ($method === 'POST') {
                $controller->processPayment();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Payment endpoint not found', null, 404);
    }
}

function handleNotificationRoutes($method, $action) {
    $controller = new NotificationController();
    
    switch ($action) {
        case 'list':
            if ($method === 'GET') {
                $controller->getNotifications();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'unread-count':
            if ($method === 'GET') {
                $controller->getUnreadCount();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'create':
            if ($method === 'POST') {
                $controller->createNotification();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'mark-read':
            if ($method === 'POST') {
                $controller->markAsRead();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Notification endpoint not found', null, 404);
    }
}

function handleFileRoutes($method, $action) {
    $controller = new FileController();
    
    switch ($action) {
        case 'upload':
            if ($method === 'POST') {
                $controller->uploadFile();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;

        case 'get':
            if ($method === 'GET') {
                $controller->getFile();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'File endpoint not found', null, 404);
    }
}

function handleAnalyticsRoutes($method, $action) {
    $controller = new AnalyticsController();
    
    switch ($action) {
        case 'stats':
            if ($method === 'GET') {
                $controller->getStatistics();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'entry-trends':
            if ($method === 'GET') {
                $controller->getEntryTrends();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'revenue-distribution':
            if ($method === 'GET') {
                $controller->getRevenueDistribution();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'performance-metrics':
            if ($method === 'GET') {
                $controller->getPerformanceMetrics();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Analytics endpoint not found', null, 404);
    }
}

function handleAuditRoutes($method, $action) {
    $controller = new ActivityLogController();
    
    switch ($action) {
        case 'logs':
            if ($method === 'GET') {
                $controller->getLogs();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Audit endpoint not found', null, 404);
    }
}

function handleSettingsRoutes($method, $action) {
    $controller = new SettingsController();
    
    switch ($action) {
        case 'get':
            if ($method === 'GET') {
                $controller->getSettings();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        case 'update':
            if ($method === 'PUT') {
                $controller->updateSettings();
            } else {
                Response::json(false, 'Method not allowed', null, 405);
            }
            break;
            
        default:
            Response::json(false, 'Settings endpoint not found', null, 404);
    }
}
