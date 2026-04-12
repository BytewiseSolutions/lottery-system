<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$requestedUserId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;

try {
    $authenticatedUser = JWT::authenticate();

    if ($requestedUserId) {
        handleProfileStats($db, $authenticatedUser, $requestedUserId);
        exit;
    }

    ensureAdminAccess($authenticatedUser);
    handleAdminStats($db);
} catch (Exception $e) {
    error_log("User stats error: " . $e->getMessage());

    if (http_response_code() < 400) {
        http_response_code(500);
    }

    echo json_encode([
        'error' => http_response_code() === 500
            ? 'Failed to retrieve user statistics'
            : ($e->getMessage() ?: 'Unable to retrieve user statistics')
    ]);
}

function handleProfileStats(PDO $db, array $authenticatedUser, int $requestedUserId): void {
    $authenticatedUserId = (int)($authenticatedUser['id'] ?? 0);
    $isAdmin = isAdminUser($authenticatedUser);

    if ($requestedUserId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid user ID required']);
        return;
    }

    if (!$isAdmin && $authenticatedUserId !== $requestedUserId) {
        http_response_code(403);
        echo json_encode(['error' => 'You can only view your own statistics']);
        return;
    }

    $entriesQuery = "SELECT COUNT(*) as total FROM entry WHERE user_id = ?";
    $stmt = $db->prepare($entriesQuery);
    $stmt->execute([$requestedUserId]);
    $totalEntries = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $totalWinnings = 0;
    if (tableExists($db, 'winner') && columnExists($db, 'winner', 'prize_amount') && columnExists($db, 'winner', 'user_id')) {
        $winningsQuery = "SELECT COALESCE(SUM(prize_amount), 0) as total FROM winner WHERE user_id = ?";
        $stmt = $db->prepare($winningsQuery);
        $stmt->execute([$requestedUserId]);
        $totalWinnings = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    $memberSince = null;
    if (columnExists($db, 'user', 'created_at')) {
        $userQuery = "SELECT created_at FROM user WHERE id = ?";
        $stmt = $db->prepare($userQuery);
        $stmt->execute([$requestedUserId]);
        $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $memberSince = $userResult['created_at'] ?? null;
    }

    echo json_encode([
        'success' => true,
        'totalEntries' => (int)$totalEntries,
        'totalWinnings' => number_format((float)$totalWinnings, 2, '.', ''),
        'memberSince' => $memberSince
    ]);
}

function handleAdminStats(PDO $db): void {
    $stats = [];
    $hasIsActiveColumn = columnExists($db, 'user', 'is_active');
    $hasEmailVerifiedColumn = columnExists($db, 'user', 'email_verified');
    $hasPhoneVerifiedColumn = columnExists($db, 'user', 'phone_verified');
    $hasCreatedAtColumn = columnExists($db, 'user', 'created_at');
    $hasRoleColumn = columnExists($db, 'user', 'role');
    $hasCountryColumn = columnExists($db, 'user', 'country');
    $hasLastLoginColumn = columnExists($db, 'user', 'last_login');
    $hasVoteTable = tableExists($db, 'vote');

    $stmt = $db->prepare("SELECT COUNT(*) as total FROM user");
    $stmt->execute();
    $stats['total_users'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    if ($hasIsActiveColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE is_active = 1");
        $stmt->execute();
        $stats['active_users'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    } else {
        $stats['active_users'] = $stats['total_users'];
    }

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
        $stats['verified_users'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    } else {
        $stats['verified_users'] = 0;
    }

    if ($hasCreatedAtColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $stats['new_users_today'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $stats['new_users_this_week'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['new_users_this_month'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    } else {
        $stats['new_users_today'] = 0;
        $stats['new_users_this_week'] = 0;
        $stats['new_users_this_month'] = 0;
    }

    if ($hasRoleColumn) {
        $stmt = $db->prepare("
            SELECT COALESCE(role, 'user') as role, COUNT(*) as count
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

    if ($hasCountryColumn) {
        $stmt = $db->prepare("
            SELECT country, COUNT(*) as count
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

    if ($hasCreatedAtColumn) {
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
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

    if ($hasVoteTable) {
        $stmt = $db->prepare("SELECT COUNT(DISTINCT user_id) as users_with_votes FROM vote");
        $stmt->execute();
        $stats['users_with_votes'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['users_with_votes'] ?? 0);
    } else {
        $stats['users_with_votes'] = 0;
    }

    if ($hasLastLoginColumn) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE last_login IS NULL");
        $stmt->execute();
        $stats['users_never_logged_in'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $stats['users_active_last_7_days'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM user WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['users_active_last_30_days'] = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    } else {
        $stats['users_never_logged_in'] = $stats['total_users'];
        $stats['users_active_last_7_days'] = 0;
        $stats['users_active_last_30_days'] = 0;
    }

    if ($hasVoteTable) {
        $stmt = $db->prepare("
            SELECT COALESCE(AVG(vote_count), 0) as avg_votes
            FROM (
                SELECT user_id, COUNT(*) as vote_count
                FROM vote
                GROUP BY user_id
            ) as user_votes
        ");
        $stmt->execute();
        $stats['avg_votes_per_user'] = round((float)($stmt->fetch(PDO::FETCH_ASSOC)['avg_votes'] ?? 0), 2);

        $stmt = $db->prepare("
            SELECT u.full_name, u.email, COUNT(v.id) as vote_count
            FROM user u
            JOIN vote v ON u.id = v.user_id
            GROUP BY u.id, u.full_name, u.email
            ORDER BY vote_count DESC
            LIMIT 5
        ");
        $stmt->execute();
        $stats['top_voters'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stats['avg_votes_per_user'] = 0;
        $stats['top_voters'] = [];
    }

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
}

function ensureAdminAccess(array $user): void {
    if (!isAdminUser($user)) {
        http_response_code(403);
        throw new RuntimeException('Admin access required');
    }
}

function isAdminUser(array $user): bool {
    return ($user['role'] ?? null) === 'admin'
        || ($user['email'] ?? null) === 'admin@totalfreelotto.com';
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
