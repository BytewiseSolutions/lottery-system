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
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $pathParts = explode('/', trim($path, '/'));
            $userId = end($pathParts);

            if (is_numeric($userId) && (int)$userId > 0) {
                handleGetUser($db, (int)$userId);
            } else {
                handleGetUsers($db);
            }
            break;
            
        case 'POST':
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (strpos($path, '/bulk-action') !== false) {
                handleBulkAction($db);
            } else {
                handleCreateUser($db);
            }
            break;
            
        case 'PUT':
            handleUpdateUser($db);
            break;
            
        case 'PATCH':
            handlePatchUser($db);
            break;
            
        case 'DELETE':
            handleDeleteUser($db);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log("User management error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

function handleGetUsers($db) {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(1, (int)($_GET['limit'] ?? 10));
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? 'all';
    $verification = $_GET['verification'] ?? 'all';
    $role = $_GET['role'] ?? 'all';
    $country = $_GET['country'] ?? '';
    $dateFrom = $_GET['date_from'] ?? '';
    $dateTo = $_GET['date_to'] ?? '';
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortOrder = $_GET['sort_order'] ?? 'desc';
    
    $offset = ($page - 1) * $limit;

    $hasCountryColumn = columnExists($db, 'user', 'country');
    $hasIsActiveColumn = columnExists($db, 'user', 'is_active');
    $hasEmailVerifiedColumn = columnExists($db, 'user', 'email_verified');
    $hasPhoneVerifiedColumn = columnExists($db, 'user', 'phone_verified');
    $hasRoleColumn = columnExists($db, 'user', 'role');
    $hasLastLoginColumn = columnExists($db, 'user', 'last_login');
    $hasUpdatedAtColumn = columnExists($db, 'user', 'updated_at');
    $hasProfilePictureColumn = columnExists($db, 'user', 'profile_picture');
    $hasCreatedAtColumn = columnExists($db, 'user', 'created_at');
    $hasPhoneColumn = columnExists($db, 'user', 'phone');
    
    // Build WHERE conditions
    $conditions = [];
    $params = [];
    
    if (!empty($search)) {
        $searchColumns = ["u.full_name LIKE ?", "u.email LIKE ?"];
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;

        if ($hasPhoneColumn) {
            $searchColumns[] = "u.phone LIKE ?";
            $params[] = $searchTerm;
        }

        $conditions[] = "(" . implode(' OR ', $searchColumns) . ")";
    }
    
    if ($status !== 'all' && $hasIsActiveColumn) {
        $conditions[] = "u.is_active = ?";
        $params[] = ($status === 'active') ? 1 : 0;
    }
    
    if ($verification !== 'all' && ($hasEmailVerifiedColumn || $hasPhoneVerifiedColumn)) {
        $emailVerifiedExpr = $hasEmailVerifiedColumn ? 'u.email_verified' : '0';
        $phoneVerifiedExpr = $hasPhoneVerifiedColumn ? 'u.phone_verified' : '0';
        if ($verification === 'verified') {
            $conditions[] = "({$emailVerifiedExpr} = 1 OR {$phoneVerifiedExpr} = 1)";
        } else {
            $conditions[] = "({$emailVerifiedExpr} = 0 AND {$phoneVerifiedExpr} = 0)";
        }
    }
    
    if ($role !== 'all' && $hasRoleColumn) {
        $conditions[] = "u.role = ?";
        $params[] = $role;
    }
    
    if (!empty($country) && $hasCountryColumn) {
        $conditions[] = "u.country = ?";
        $params[] = $country;
    }
    
    if (!empty($dateFrom) && $hasCreatedAtColumn) {
        $conditions[] = "DATE(u.created_at) >= ?";
        $params[] = $dateFrom;
    }
    
    if (!empty($dateTo) && $hasCreatedAtColumn) {
        $conditions[] = "DATE(u.created_at) <= ?";
        $params[] = $dateTo;
    }
    
    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    $hasVoteTable = tableExists($db, 'vote');
    $hasWinnerTable = tableExists($db, 'winner');
    $hasActivityLogTable = tableExists($db, 'activity_log');

    $voteSelect = $hasVoteTable ? 'COALESCE(vote_stats.total_votes, 0)' : '0';
    $winningsSelect = $hasWinnerTable ? 'COALESCE(win_stats.total_winnings, 0)' : '0';
    $loginCountSelect = $hasActivityLogTable ? 'COALESCE(login_stats.login_count, 0)' : '0';
    $countrySelect = $hasCountryColumn ? 'u.country' : 'NULL';
    $isActiveSelect = $hasIsActiveColumn ? 'u.is_active' : '1';
    $emailVerifiedSelect = $hasEmailVerifiedColumn ? 'u.email_verified' : '0';
    $phoneVerifiedSelect = $hasPhoneVerifiedColumn ? 'u.phone_verified' : '0';
    $roleSelect = $hasRoleColumn ? 'u.role' : "'user'";
    $lastLoginSelect = $hasLastLoginColumn ? 'u.last_login' : 'NULL';
    $createdAtSelect = $hasCreatedAtColumn ? 'u.created_at' : 'NULL';
    $updatedAtSelect = $hasUpdatedAtColumn ? 'u.updated_at' : 'NULL';
    $profilePictureSelect = $hasProfilePictureColumn ? 'u.profile_picture' : 'NULL';

    $sortExpressions = [
        'created_at' => $hasCreatedAtColumn ? 'u.created_at' : 'u.id',
        'full_name' => 'u.full_name',
        'email' => 'u.email',
        'last_login' => $hasLastLoginColumn ? 'u.last_login' : ($hasCreatedAtColumn ? 'u.created_at' : 'u.id'),
        'total_votes' => $voteSelect
    ];
    $sortExpression = $sortExpressions[$sortBy] ?? ($hasCreatedAtColumn ? 'u.created_at' : 'u.id');
    
    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM user u " . $whereClause;
    $stmt = $db->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $voteJoin = $hasVoteTable ? "
            LEFT JOIN (
                SELECT user_id, COUNT(*) as total_votes 
                FROM vote 
                GROUP BY user_id
            ) vote_stats ON u.id = vote_stats.user_id" : '';

    $winnerJoin = $hasWinnerTable ? "
            LEFT JOIN (
                SELECT user_id, SUM(prize_amount) as total_winnings 
                FROM winner 
                GROUP BY user_id
            ) win_stats ON u.id = win_stats.user_id" : '';

    $loginJoin = $hasActivityLogTable ? "
            LEFT JOIN (
                SELECT user_id, COUNT(*) as login_count 
                FROM activity_log 
                WHERE action = 'login'
                GROUP BY user_id
            ) login_stats ON u.id = login_stats.user_id" : '';
    
    // Get users with additional stats
    $sql = "SELECT 
                u.id, u.full_name, u.email, " . ($hasPhoneColumn ? "u.phone" : "NULL") . " as phone, {$countrySelect} as country, {$isActiveSelect} as is_active, 
                {$emailVerifiedSelect} as email_verified, {$phoneVerifiedSelect} as phone_verified, {$roleSelect} as role, {$lastLoginSelect} as last_login, {$createdAtSelect} as created_at, {$updatedAtSelect} as updated_at,
                {$profilePictureSelect} as profile_picture,
                {$voteSelect} as total_votes,
                {$winningsSelect} as total_winnings,
                {$loginCountSelect} as login_count
            FROM user u
            {$voteJoin}
            {$winnerJoin}
            {$loginJoin}
            " . $whereClause . "
            ORDER BY {$sortExpression} {$sortOrder}
            LIMIT {$limit} OFFSET {$offset}";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get countries for filter
    $countries = [];
    if ($hasCountryColumn) {
        $countrySql = "SELECT DISTINCT country FROM user WHERE country IS NOT NULL AND country != '' ORDER BY country";
        $stmt = $db->prepare($countrySql);
        $stmt->execute();
        $countries = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'total' => (int)$total,
        'totalPages' => ceil($total / $limit),
        'currentPage' => $page,
        'countries' => $countries
    ]);
}

function handleGetUser($db, int $userId) {
    $hasCountryColumn = columnExists($db, 'user', 'country');
    $hasIsActiveColumn = columnExists($db, 'user', 'is_active');
    $hasEmailVerifiedColumn = columnExists($db, 'user', 'email_verified');
    $hasPhoneVerifiedColumn = columnExists($db, 'user', 'phone_verified');
    $hasRoleColumn = columnExists($db, 'user', 'role');
    $hasLastLoginColumn = columnExists($db, 'user', 'last_login');
    $hasUpdatedAtColumn = columnExists($db, 'user', 'updated_at');
    $hasProfilePictureColumn = columnExists($db, 'user', 'profile_picture');
    $hasCreatedAtColumn = columnExists($db, 'user', 'created_at');
    $hasPhoneColumn = columnExists($db, 'user', 'phone');
    $hasVoteTable = tableExists($db, 'vote');
    $hasWinnerTable = tableExists($db, 'winner');
    $hasActivityLogTable = tableExists($db, 'activity_log');

    $voteSelect = $hasVoteTable ? 'COALESCE(vote_stats.total_votes, 0)' : '0';
    $winningsSelect = $hasWinnerTable ? 'COALESCE(win_stats.total_winnings, 0)' : '0';
    $loginCountSelect = $hasActivityLogTable ? 'COALESCE(login_stats.login_count, 0)' : '0';
    $countrySelect = $hasCountryColumn ? 'u.country' : 'NULL';
    $isActiveSelect = $hasIsActiveColumn ? 'u.is_active' : '1';
    $emailVerifiedSelect = $hasEmailVerifiedColumn ? 'u.email_verified' : '0';
    $phoneVerifiedSelect = $hasPhoneVerifiedColumn ? 'u.phone_verified' : '0';
    $roleSelect = $hasRoleColumn ? 'u.role' : "'user'";
    $lastLoginSelect = $hasLastLoginColumn ? 'u.last_login' : 'NULL';
    $createdAtSelect = $hasCreatedAtColumn ? 'u.created_at' : 'NULL';
    $updatedAtSelect = $hasUpdatedAtColumn ? 'u.updated_at' : 'NULL';
    $profilePictureSelect = $hasProfilePictureColumn ? 'u.profile_picture' : 'NULL';

    $voteJoin = $hasVoteTable ? "
            LEFT JOIN (
                SELECT user_id, COUNT(*) as total_votes
                FROM vote
                GROUP BY user_id
            ) vote_stats ON u.id = vote_stats.user_id" : '';

    $winnerJoin = $hasWinnerTable ? "
            LEFT JOIN (
                SELECT user_id, SUM(prize_amount) as total_winnings
                FROM winner
                GROUP BY user_id
            ) win_stats ON u.id = win_stats.user_id" : '';

    $loginJoin = $hasActivityLogTable ? "
            LEFT JOIN (
                SELECT user_id, COUNT(*) as login_count
                FROM activity_log
                WHERE action = 'login'
                GROUP BY user_id
            ) login_stats ON u.id = login_stats.user_id" : '';

    $sql = "SELECT
                u.id,
                u.full_name,
                u.email,
                " . ($hasPhoneColumn ? "u.phone" : "NULL") . " as phone,
                {$countrySelect} as country,
                {$isActiveSelect} as is_active,
                {$emailVerifiedSelect} as email_verified,
                {$phoneVerifiedSelect} as phone_verified,
                {$roleSelect} as role,
                {$lastLoginSelect} as last_login,
                {$createdAtSelect} as created_at,
                {$updatedAtSelect} as updated_at,
                {$profilePictureSelect} as profile_picture,
                {$voteSelect} as total_votes,
                {$winningsSelect} as total_winnings,
                {$loginCountSelect} as login_count
            FROM user u
            {$voteJoin}
            {$winnerJoin}
            {$loginJoin}
            WHERE u.id = ?
            LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
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

function handleCreateUser($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($input['full_name']) || empty($input['email']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Full name, email, and password are required']);
        return;
    }
    
    // Check if email already exists
    $checkStmt = $db->prepare("SELECT id FROM user WHERE email = ?");
    $checkStmt->execute([$input['email']]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

    $columns = ['full_name', 'email', 'password'];
    $placeholders = ['?', '?', '?'];
    $values = [
        $input['full_name'],
        $input['email'],
        $hashedPassword
    ];

    appendUserFieldIfColumnExists($db, $columns, $placeholders, $values, 'phone', normalizeNullableString($input['phone'] ?? null));
    appendUserFieldIfColumnExists($db, $columns, $placeholders, $values, 'country', normalizeNullableString($input['country'] ?? null));
    appendUserFieldIfColumnExists($db, $columns, $placeholders, $values, 'role', $input['role'] ?? 'user');
    appendUserFieldIfColumnExists($db, $columns, $placeholders, $values, 'is_active', !empty($input['is_active']) ? 1 : 0);
    appendUserFieldIfColumnExists($db, $columns, $placeholders, $values, 'email_verified', !empty($input['email_verified']) ? 1 : 0);
    appendUserFieldIfColumnExists($db, $columns, $placeholders, $values, 'phone_verified', !empty($input['phone_verified']) ? 1 : 0);

    if (columnExists($db, 'user', 'created_at')) {
        $columns[] = 'created_at';
        $placeholders[] = 'NOW()';
    }

    $stmt = $db->prepare(sprintf(
        'INSERT INTO user (%s) VALUES (%s)',
        implode(', ', $columns),
        implode(', ', $placeholders)
    ));

    $stmt->execute($values);
    
    $userId = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'id' => $userId,
        'message' => 'User created successfully'
    ]);
}

function handleUpdateUser($db) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    $userId = end($pathParts);
    
    if (!is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user ID']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Check if user exists
    $checkStmt = $db->prepare("SELECT id FROM user WHERE id = ?");
    $checkStmt->execute([$userId]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }
    
    // Check if email is taken by another user
    if (!empty($input['email'])) {
        $emailCheckStmt = $db->prepare("SELECT id FROM user WHERE email = ? AND id != ?");
        $emailCheckStmt->execute([$input['email'], $userId]);
        if ($emailCheckStmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }
    }
    
    $setClauses = ['full_name = ?', 'email = ?'];
    $values = [
        $input['full_name'],
        $input['email']
    ];

    appendUserUpdateIfColumnExists($db, $setClauses, $values, 'phone', normalizeNullableString($input['phone'] ?? null));
    appendUserUpdateIfColumnExists($db, $setClauses, $values, 'country', normalizeNullableString($input['country'] ?? null));
    appendUserUpdateIfColumnExists($db, $setClauses, $values, 'role', $input['role'] ?? 'user');
    appendUserUpdateIfColumnExists($db, $setClauses, $values, 'is_active', !empty($input['is_active']) ? 1 : 0);
    appendUserUpdateIfColumnExists($db, $setClauses, $values, 'email_verified', !empty($input['email_verified']) ? 1 : 0);
    appendUserUpdateIfColumnExists($db, $setClauses, $values, 'phone_verified', !empty($input['phone_verified']) ? 1 : 0);

    if (columnExists($db, 'user', 'updated_at')) {
        $setClauses[] = 'updated_at = NOW()';
    }

    $values[] = $userId;

    $stmt = $db->prepare(sprintf(
        'UPDATE user SET %s WHERE id = ?',
        implode(', ', $setClauses)
    ));

    $stmt->execute($values);
    
    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);
}

function handlePatchUser($db) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    if (strpos($path, '/toggle-status') !== false) {
        $userId = extractUserIdFromPath($path, '/toggle-status');
        toggleUserStatus($db, $userId);
    } elseif (strpos($path, '/send-verification') !== false) {
        $userId = extractUserIdFromPath($path, '/send-verification');
        sendVerificationEmail($db, $userId);
    } elseif (strpos($path, '/reset-password') !== false) {
        $userId = extractUserIdFromPath($path, '/reset-password');
        resetUserPassword($db, $userId);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid patch operation']);
    }
}

function handleDeleteUser($db) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    $userId = end($pathParts);
    
    if (!is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user ID']);
        return;
    }
    
    // Check if user exists
    $checkStmt = $db->prepare("SELECT id FROM user WHERE id = ?");
    $checkStmt->execute([$userId]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }
    
    // Delete user (this will cascade to related records if foreign keys are set up)
    $stmt = $db->prepare("DELETE FROM user WHERE id = ?");
    $stmt->execute([$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
}

function handleBulkAction($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $action = $input['action'] ?? '';
    $userIds = $input['user_ids'] ?? [];
    
    if (empty($action) || empty($userIds)) {
        http_response_code(400);
        echo json_encode(['error' => 'Action and user IDs are required']);
        return;
    }
    
    $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
    
    switch ($action) {
        case 'activate':
            $stmt = $db->prepare("UPDATE user SET is_active = 1 WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        case 'deactivate':
            $stmt = $db->prepare("UPDATE user SET is_active = 0 WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        case 'verify_email':
            $stmt = $db->prepare("UPDATE user SET email_verified = 1 WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        case 'verify_phone':
            $stmt = $db->prepare("UPDATE user SET phone_verified = 1 WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        case 'send_verification':
            // In a real implementation, you would send verification emails here
            foreach ($userIds as $userId) {
                // sendVerificationEmailToUser($userId);
            }
            break;
            
        case 'delete':
            $stmt = $db->prepare("DELETE FROM user WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid bulk action']);
            return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Bulk action '{$action}' executed successfully on " . count($userIds) . " users"
    ]);
}

function toggleUserStatus($db, $userId) {
    if (!is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user ID']);
        return;
    }
    
    $stmt = $db->prepare("UPDATE user SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'User status toggled successfully'
    ]);
}

function sendVerificationEmail($db, $userId) {
    if (!is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user ID']);
        return;
    }
    
    // Get user email
    $stmt = $db->prepare("SELECT email, full_name FROM user WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }
    
    // In a real implementation, you would send the verification email here
    // For now, we'll just simulate it
    
    echo json_encode([
        'success' => true,
        'message' => 'Verification email sent successfully'
    ]);
}

function resetUserPassword($db, $userId) {
    if (!is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user ID']);
        return;
    }
    
    // Generate new password
    $newPassword = generateRandomPassword();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $setClauses = ['password = ?'];
    $values = [$hashedPassword];

    if (columnExists($db, 'user', 'updated_at')) {
        $setClauses[] = 'updated_at = NOW()';
    }

    $values[] = $userId;

    $stmt = $db->prepare(sprintf(
        'UPDATE user SET %s WHERE id = ?',
        implode(', ', $setClauses)
    ));
    $stmt->execute($values);
    
    echo json_encode([
        'success' => true,
        'new_password' => $newPassword,
        'message' => 'Password reset successfully'
    ]);
}

function extractUserIdFromPath($path, $suffix) {
    $path = str_replace($suffix, '', $path);
    $pathParts = explode('/', trim($path, '/'));
    return end($pathParts);
}

function appendUserFieldIfColumnExists(PDO $db, array &$columns, array &$placeholders, array &$values, string $column, $value): void {
    if (!columnExists($db, 'user', $column)) {
        return;
    }

    $columns[] = $column;
    $placeholders[] = '?';
    $values[] = $value;
}

function appendUserUpdateIfColumnExists(PDO $db, array &$setClauses, array &$values, string $column, $value): void {
    if (!columnExists($db, 'user', $column)) {
        return;
    }

    $setClauses[] = "{$column} = ?";
    $values[] = $value;
}

function normalizeNullableString($value): ?string {
    if ($value === null) {
        return null;
    }

    $trimmed = trim((string)$value);
    return $trimmed === '' ? null : $trimmed;
}

function generateRandomPassword($length = 12) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
?>
