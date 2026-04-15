<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$pathInfo = parse_url($requestUri, PHP_URL_PATH);
$pathParts = explode('/', trim($pathInfo, '/'));

function refresh_leading_numbers_snapshot(PDO $db, string $lottery, string $drawDate): void
{
    $lottery = trim($lottery);
    $drawDate = substr(trim($drawDate), 0, 10);

    if ($lottery === '' || $drawDate === '') {
        return;
    }

    $service = new App\Application\Voting\LeadingNumbersService(
        new App\Domain\Voting\VoteRepository($db),
        new App\Domain\Voting\AdminVoteRepository($db),
        new App\Domain\Voting\LeadingNumbersSnapshotRepository($db)
    );

    $service->refreshSnapshot($lottery, $drawDate);
}

if ($method === 'POST') {
    $user = JWT::authenticate();
    
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $adminId = $user['id'];
    $lottery = $data['lottery'] ?? '';
    $voteDate = $data['voteDate'] ?? date('Y-m-d');
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (isset($data['votingData'])) {
        $votingData = $data['votingData'];
        $totalVotes = $data['totalVotes'] ?? 0;
        $mainNumbers = $data['mainNumbers'] ?? [];
        $bonusNumbers = $data['bonusNumbers'] ?? [];
        $numbers = $data['numbers'] ?? [];
        $bonusNums = $data['bonusNumbers'] ?? [];
        
        if (empty($lottery) || empty($votingData) || $totalVotes <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        
        // Insert with both old and new format for compatibility
        $stmt = $db->prepare("INSERT INTO admin_vote (admin_id, lottery, numbers, bonus_numbers, allocated_votes, voting_data, total_votes, vote_date, draw_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $adminId, 
            $lottery, 
            json_encode($numbers), 
            json_encode($bonusNums), 
            $totalVotes,
            json_encode($votingData), 
            $totalVotes, 
            date('Y-m-d'),
            $voteDate
        ]);
    } else {
        // Handle legacy format
        $numbers = $data['numbers'] ?? [];
        $bonusNumbers = $data['bonusNumbers'] ?? [];
        $allocatedVotes = $data['allocatedVotes'] ?? 0;
        
        if (empty($lottery) || empty($numbers) || empty($bonusNumbers) || $allocatedVotes <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        
        $stmt = $db->prepare("INSERT INTO admin_vote (admin_id, lottery, numbers, bonus_numbers, allocated_votes, vote_date, draw_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$adminId, $lottery, json_encode($numbers), json_encode($bonusNumbers), $allocatedVotes, date('Y-m-d'), $voteDate]);
    }

    refresh_leading_numbers_snapshot($db, $lottery, $voteDate);
    
    echo json_encode(['success' => true, 'message' => 'Votes allocated successfully']);
    
} elseif ($method === 'GET') {
    $user = JWT::authenticate();
    
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("SELECT id, lottery, numbers, bonus_numbers, allocated_votes, voting_data, total_votes, vote_date, draw_date, created_at FROM admin_vote ORDER BY created_at DESC");
    $stmt->execute();
    $adminVotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($adminVotes as $vote) {
        $result[] = [
            'id' => $vote['id'],
            'lottery' => $vote['lottery'],
            'numbers' => $vote['numbers'] ? json_decode($vote['numbers']) : null,
            'bonusNumbers' => $vote['bonus_numbers'] ? json_decode($vote['bonus_numbers']) : null,
            'allocatedVotes' => $vote['allocated_votes'],
            'votingData' => $vote['voting_data'] ? json_decode($vote['voting_data']) : null,
            'totalVotes' => $vote['total_votes'],
            'voteDate' => $vote['vote_date'], // Creation date
            'drawDate' => $vote['draw_date'], // Actual lottery draw date
            'createdAt' => $vote['created_at']
        ];
    }
    
    echo json_encode(['adminVotes' => $result]);
    
} elseif ($method === 'PUT') {
    $user = JWT::authenticate();
    
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $voteId = $data['id'] ?? 0;
    $lottery = $data['lottery'] ?? '';
    $voteDate = $data['voteDate'] ?? '';
    $numbers = $data['numbers'] ?? [];
    $bonusNumbers = $data['bonusNumbers'] ?? [];
    $allocatedVotes = $data['allocatedVotes'] ?? 0;
    $votingData = $data['votingData'] ?? null;
    $totalVotes = $data['totalVotes'] ?? $allocatedVotes;
    
    // Validation
    if (!$voteId || empty($lottery) || empty($voteDate) || empty($numbers) || empty($bonusNumbers) || $allocatedVotes <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    // Validate numbers array lengths
    if (count($numbers) !== 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Must have exactly 5 main numbers']);
        exit;
    }
    
    if (count($bonusNumbers) !== 2) {
        http_response_code(400);
        echo json_encode(['error' => 'Must have exactly 2 bonus numbers']);
        exit;
    }
    
    // Validate number ranges (1-75)
    $allNumbers = array_merge($numbers, $bonusNumbers);
    foreach ($allNumbers as $num) {
        if (!is_numeric($num) || $num < 1 || $num > 75) {
            http_response_code(400);
            echo json_encode(['error' => 'All numbers must be between 1 and 75']);
            exit;
        }
    }
    
    // Check for duplicate numbers between main and bonus
    $duplicates = array_intersect($numbers, $bonusNumbers);
    if (!empty($duplicates)) {
        http_response_code(400);
        echo json_encode(['error' => 'Numbers cannot appear in both main and bonus sections']);
        exit;
    }
    
    // Check for duplicate numbers within each section
    if (count($numbers) !== count(array_unique($numbers))) {
        http_response_code(400);
        echo json_encode(['error' => 'Duplicate numbers found in main numbers']);
        exit;
    }
    
    if (count($bonusNumbers) !== count(array_unique($bonusNumbers))) {
        http_response_code(400);
        echo json_encode(['error' => 'Duplicate numbers found in bonus numbers']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if vote allocation exists
    $checkStmt = $db->prepare("SELECT id, lottery, draw_date FROM admin_vote WHERE id = ?");
    $checkStmt->execute([$voteId]);
    $existingVote = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingVote) {
        http_response_code(404);
        echo json_encode(['error' => 'Vote allocation not found']);
        exit;
    }
    
    // Check for duplicate lottery/date combination (excluding current record) - REMOVED
    // Admins can now create multiple allocations for the same lottery and date
    
    // Update the vote allocation
    $updateStmt = $db->prepare("
        UPDATE admin_vote 
        SET lottery = ?, 
            numbers = ?, 
            bonus_numbers = ?, 
            allocated_votes = ?, 
            voting_data = ?, 
            total_votes = ?, 
            draw_date = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $success = $updateStmt->execute([
        $lottery,
        json_encode($numbers),
        json_encode($bonusNumbers),
        $allocatedVotes,
        $votingData ? json_encode($votingData) : null,
        $totalVotes,
        $voteDate,
        $voteId
    ]);
    
    if ($success) {
        if (
            $existingVote['lottery'] !== $lottery
            || substr((string) $existingVote['draw_date'], 0, 10) !== substr((string) $voteDate, 0, 10)
        ) {
            refresh_leading_numbers_snapshot($db, (string) $existingVote['lottery'], (string) $existingVote['draw_date']);
        }

        refresh_leading_numbers_snapshot($db, $lottery, $voteDate);

        echo json_encode([
            'success' => true, 
            'message' => 'Vote allocation updated successfully',
            'data' => [
                'id' => $voteId,
                'lottery' => $lottery,
                'numbers' => $numbers,
                'bonusNumbers' => $bonusNumbers,
                'allocatedVotes' => $allocatedVotes,
                'totalVotes' => $totalVotes,
                'drawDate' => $voteDate
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update vote allocation']);
    }
    
} elseif ($method === 'DELETE') {
    $user = JWT::authenticate();
    
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    
    // Get vote ID from query parameter (set by .htaccess rewrite rule)
    $voteId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if (!$voteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Vote ID is required']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if vote exists
    $checkStmt = $db->prepare("SELECT id, lottery, draw_date FROM admin_vote WHERE id = ?");
    $checkStmt->execute([$voteId]);
    $existingVote = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingVote) {
        http_response_code(404);
        echo json_encode(['error' => 'Vote allocation not found']);
        exit;
    }
    
    // Delete the vote
    $deleteStmt = $db->prepare("DELETE FROM admin_vote WHERE id = ?");
    $deleteStmt->execute([$voteId]);

    refresh_leading_numbers_snapshot($db, (string) $existingVote['lottery'], (string) $existingVote['draw_date']);
    
    echo json_encode(['success' => true, 'message' => 'Vote allocation deleted successfully']);
}
?>
