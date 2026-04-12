<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

header('Content-Type: application/json');

try {
    $user = JWT::authenticate();

    if (($user['role'] ?? null) !== 'admin' && ($user['email'] ?? null) !== 'admin@totalfreelotto.com') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Authorization token required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // Get comprehensive user statistics
    $stats = [];
    $hasIsActiveColumn = columnExists($db, 'user', 'is_active');
    $hasEmailVerifiedColumn = columnExists($db, 'user', 'email_verified');
    $hasPhoneVerifiedColumn = columnExists($db, 'user', 'phone_verified');
    $hasCreatedAtColumn = columnExists($db, 'user', 'created_at');
    $hasRoleColumn = columnExists($db, 'user', 'role');
    $hasCountryColumn = columnExists($db, 'user', 'country');
    $hasLastLoginColumn = columnExists($db, 'user', 'last_login');
    $hasVoteTable = tableExists($db, 'vote');
    
    // Total users
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM user");
    $stmt->execute();
    $stats['total_users'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Active users
    if ($hasIsActiveColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE is_active = 1");
        $stmt->execute();
        $stats['active_users'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['active_users'] = $stats['total_users'];
    }
    
    // Verified users (email OR phone verified)
    if ($hasEmailVerifiedColumn || $hasPhoneVerifiedColumn) {
        $verificationChecks = [];
        if ($hasEmailVerifiedColumn) {
            $verificationChecks[] = "email_verified = 1";
        }
        if ($hasPhoneVerifiedColumn) {
            $verificationChecks[] = "phone_verified = 1";
        }

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE " . implode(' OR ', $verificationChecks));
        $stmt->execute();
        $stats['verified_users'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['verified_users'] = 0;
    }
    
    // New users today
    if ($hasCreatedAtColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $stats['new_users_today'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['new_users_today'] = 0;
    }
    
    // New users this week
    if ($hasCreatedAtColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $stats['new_users_this_week'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['new_users_this_week'] = 0;
    }
    
    // New users this month
    if ($hasCreatedAtColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['new_users_this_month'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['new_users_this_month'] = 0;
    }
    
    // Additional stats
    
    // Users by role
    if ($hasRoleColumn) {
        $stmt = $db->prepare("
            SELECT 
                COALESCE(role, 'user') as role, 
                COUNT(*) as count 
            FROM user 
            GROUP BY role
        ");
        $stmt->execute();
        $stats['users_by_role'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stats['users_by_role'] = [[
            'role' => 'user',
            'count' => $stats['total_users']
        ]];
    }
    
    // Users by country (top 10)
    if ($hasCountryColumn) {
        $stmt = $db->prepare("
            SELECT 
                country, 
                COUNT(*) as count 
            FROM user 
            WHERE country IS NOT NULL AND country != ''
            GROUP BY country 
            ORDER BY count DESC 
            LIMIT 10
        ");
        $stmt->execute();
        $stats['users_by_country'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stats['users_by_country'] = [];
    }
    
    // User registration trend (last 30 days)
    if ($hasCreatedAtColumn) {
        $stmt = $db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM user 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $stats['registration_trend'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stats['registration_trend'] = [];
    }
    
    // User activity stats
    if ($hasVoteTable) {
        $stmt = $db->prepare("
            SELECT 
                COUNT(DISTINCT user_id) as users_with_votes
            FROM vote
        ");
        $stmt->execute();
        $stats['users_with_votes'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['users_with_votes'];
    } else {
        $stats['users_with_votes'] = 0;
    }
    
    // Users who have never logged in
    if ($hasLastLoginColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE last_login IS NULL");
        $stmt->execute();
        $stats['users_never_logged_in'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['users_never_logged_in'] = $stats['total_users'];
    }
    
    // Users logged in last 7 days
    if ($hasLastLoginColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $stats['users_active_last_7_days'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['users_active_last_7_days'] = 0;
    }
    
    // Users logged in last 30 days
    if ($hasLastLoginColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['users_active_last_30_days'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $stats['users_active_last_30_days'] = 0;
    }
    
    // Average votes per user
    if ($hasVoteTable) {
        $stmt = $db->prepare("
            SELECT 
                COALESCE(AVG(vote_count), 0) as avg_votes
            FROM (
                SELECT user_id, COUNT(*) as vote_count
                FROM vote
                GROUP BY user_id
            ) as user_votes
        ");
        $stmt->execute();
        $stats['avg_votes_per_user'] = round((float)$stmt->fetch(PDO::FETCH_ASSOC)['avg_votes'], 2);
    } else {
        $stats['avg_votes_per_user'] = 0;
    }
    
    // Top voters (users with most votes)
    if ($hasVoteTable) {
        $stmt = $db->prepare("
            SELECT 
                u.full_name,
                u.email,
                COUNT(v.id) as vote_count
            FROM user u
            JOIN vote v ON u.id = v.user_id
            GROUP BY u.id, u.full_name, u.email
            ORDER BY vote_count DESC
            LIMIT 5
        ");
        $stmt->execute();
        $stats['top_voters'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stats['top_voters'] = [];
    }
    
    // Recent registrations (last 10)
    $recentRegistrationsSql = "
        SELECT 
            full_name,
            email,
            " . ($hasCountryColumn ? "country" : "NULL") . " as country,
            " . ($hasCreatedAtColumn ? "created_at" : "NULL") . " as created_at
        FROM user
        ORDER BY " . ($hasCreatedAtColumn ? "created_at DESC" : "id DESC") . "
        LIMIT 10
    ";
    $stmt = $db->prepare($recentRegistrationsSql);
    $stmt->execute();
    $stats['recent_registrations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    error_log("User stats error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve user statistics']);
}

function tableExists(PDO $db, string $tableName): bool {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'sqlite') {
        $stmt = $db->prepare("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = ? LIMIT 1");
        $stmt->execute([$tableName]);
        return (bool)$stmt->fetchColumn();
    }

    $stmt = $db->prepare("
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = DATABASE() AND table_name = ?
        LIMIT 1
    ");
    $stmt->execute([$tableName]);
    return (bool)$stmt->fetchColumn();
}

function columnExists(PDO $db, string $tableName, string $columnName): bool {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'sqlite') {
        $stmt = $db->query("PRAGMA table_info({$tableName})");
        $columns = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        foreach ($columns as $column) {
            if (($column['name'] ?? null) === $columnName) {
                return true;
            }
        }
        return false;
    }

    $stmt = $db->prepare("
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = ?
          AND column_name = ?
        LIMIT 1
    ");
    $stmt->execute([$tableName, $columnName]);
    return (bool)$stmt->fetchColumn();
}
?>
