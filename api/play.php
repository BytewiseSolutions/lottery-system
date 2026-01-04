<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'config/ratelimit.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

// Rate limiting per user
$rateLimit = new RateLimit($db);
$rateLimit->checkLimit($user['id'], 'play', 100, 3600); // 100 plays per hour

$data = json_decode(file_get_contents("php://input"));

if (!$data->lottery || !$data->numbers || !$data->bonusNumbers || !$data->drawDate) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

if (count($data->numbers) !== 5 || count($data->bonusNumbers) !== 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid number selection']);
    exit;
}

// Validate number ranges
foreach ($data->numbers as $num) {
    if ($num < 1 || $num > 75) {
        http_response_code(400);
        echo json_encode(['error' => 'Numbers must be between 1 and 75']);
        exit;
    }
}

foreach ($data->bonusNumbers as $num) {
    if ($num < 1 || $num > 75) {
        http_response_code(400);
        echo json_encode(['error' => 'Bonus numbers must be between 1 and 75']);
        exit;
    }
}

try {
    // Convert lottery code to full name
    $lotteryName = $data->lottery === 'mon' ? 'Monday Lotto' : 
                   ($data->lottery === 'wed' ? 'Wednesday Lotto' : 'Friday Lotto');
    
    // Check how many times user played today
    $query = "SELECT COUNT(*) as play_count FROM entries WHERE user_id = ? AND DATE(created_at) = DATE(NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute([$user['id']]);
    $playCount = $stmt->fetch(PDO::FETCH_ASSOC)['play_count'];
    
    if ($playCount >= 2 && !isset($data->humanVerified)) {
        echo json_encode([
            'requireHumanVerification' => true,
            'message' => 'Please verify you are human to continue playing'
        ]);
        exit;
    }
    
    // Insert entry
    $query = "INSERT INTO entries (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $user['id'],
        $lotteryName,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers),
        $data->drawDate
    ]);
    
    $entryId = $db->lastInsertId();
    
    // Calculate new pool amount
    $query = "SELECT COUNT(*) * 0.01 as pool_amount FROM entries WHERE lottery = ? AND draw_date = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$lotteryName, $data->drawDate]);
    $poolData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $newPoolAmount = $poolData['pool_amount'] ?: 0.01;
    
    echo json_encode([
        'success' => true,
        'message' => 'Entry submitted successfully!',
        'newPoolAmount' => number_format($newPoolAmount, 3),
        'entryId' => $entryId
    ]);
    
} catch(PDOException $exception) {
    error_log("Play error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit entry']);
}
?>