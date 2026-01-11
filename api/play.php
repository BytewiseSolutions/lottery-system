<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
// require_once 'config/ratelimit.php'; // Disabled for performance

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

// Rate limiting - disabled for performance
// $rateLimit = new RateLimit($db);
// $rateLimit->checkLimit($user['id'], 'play', 100, 3600);

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
    // Insert entry immediately - no complex processing
    $query = "INSERT INTO entries (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $user['id'],
        $data->lottery,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers),
        $data->drawDate
    ]);
    
    $entryId = $db->lastInsertId();
    
    // Send immediate response
    echo json_encode([
        'success' => true,
        'message' => 'Entry submitted successfully!',
        'entryId' => $entryId,
        'numbers' => $data->numbers,
        'bonusNumbers' => $data->bonusNumbers,
        'lottery' => $data->lottery,
        'drawDate' => $data->drawDate
    ]);
    
} catch(PDOException $exception) {
    error_log("Play error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit entry']);
}
?>