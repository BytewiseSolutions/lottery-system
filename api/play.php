<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

date_default_timezone_set('Africa/Johannesburg');

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

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
    $lotteryName = ucfirst($data->lottery) . ' Lotto';
    
    $checkDrawQuery = "SELECT draw_date FROM past_draw WHERE lottery = ? AND draw_date = ? 
                        UNION 
                        SELECT draw_date FROM upcoming_draw WHERE lottery = ? AND draw_date = ?";
    $stmt = $db->prepare($checkDrawQuery);
    $stmt->execute([$lotteryName, $data->drawDate, $lotteryName, $data->drawDate]);
    $draw = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$draw) {
        http_response_code(400);
        echo json_encode(['error' => 'This draw is no longer available for play']);
        exit;
    }

    $drawDateTime = new DateTime($draw['draw_date']);
    $now = new DateTime();
    
    if ($now >= $drawDateTime) {
        http_response_code(400);
        echo json_encode(['error' => 'Draw time has passed. You cannot play this lottery anymore.']);
        exit;
    }
    
    $query = "INSERT INTO entry (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $user['id'],
        $lotteryName,
        json_encode($data->numbers),
        json_encode($data->bonusNumbers),
        $data->drawDate
    ]);
    
    $entryId = $db->lastInsertId();
    
    error_log("Entry created: lottery=$lotteryName, date=$data->drawDate, user_id={$user['id']}");
    
    $logQuery = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    $logStmt = $db->prepare($logQuery);
    $logStmt->execute([
        $user['id'],
        'lottery_played',
        json_encode(['lottery' => $lotteryName, 'draw_date' => $data->drawDate, 'entry_id' => $entryId]),
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    $updateJackpot = "UPDATE upcoming_draw SET jackpot = jackpot + 0.01 
                      WHERE lottery = ? AND DATE(draw_date) = DATE(?) LIMIT 1";
    $stmt = $db->prepare($updateJackpot);
    $result = $stmt->execute([$lotteryName, $data->drawDate]);
    $rowsAffected = $stmt->rowCount();
    error_log("Jackpot update: lottery=$lotteryName, date=$data->drawDate, rows affected=$rowsAffected");
    
    if ($rowsAffected === 0) {
        error_log("WARNING: No rows updated! Check if lottery name and date match in upcoming_draws table");
    }

    $getJackpot = "SELECT jackpot FROM upcoming_draw WHERE lottery = ? AND DATE(draw_date) = DATE(?) LIMIT 1";
    $stmt = $db->prepare($getJackpot);
    $stmt->execute([$lotteryName, $data->drawDate]);
    $updatedJackpot = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => 'Entry submitted successfully!',
        'entryId' => $entryId,
        'numbers' => $data->numbers,
        'bonusNumbers' => $data->bonusNumbers,
        'lottery' => $data->lottery,
        'drawDate' => $data->drawDate,
        'updatedJackpot' => $updatedJackpot
    ]);
    
} catch(PDOException $exception) {
    error_log("Play error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit entry']);
}
?>