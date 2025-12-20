<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

// Check admin authentication
try {
    $user = JWT::authenticate();
    if ($user['email'] !== 'admin@totalfreelotto.com') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }
    
    // Validate required fields
    $required = ['lottery', 'drawDate', 'jackpot', 'winningNumbers', 'bonusNumbers', 'winners'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing field: $field"]);
            exit;
        }
    }
    
    // Validate numbers
    if (count($input['winningNumbers']) !== 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Must have exactly 5 winning numbers']);
        exit;
    }
    
    if (count($input['bonusNumbers']) !== 2) {
        http_response_code(400);
        echo json_encode(['error' => 'Must have exactly 2 bonus numbers']);
        exit;
    }
    
    // Create results table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        draw_date DATE NOT NULL,
        winning_numbers JSON NOT NULL,
        bonus_numbers JSON NOT NULL,
        jackpot VARCHAR(20) NOT NULL,
        winners INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($createTable);
    
    // Insert result
    $query = "INSERT INTO results (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    $success = $stmt->execute([
        $input['lottery'],
        $input['drawDate'],
        json_encode($input['winningNumbers']),
        json_encode($input['bonusNumbers']),
        $input['jackpot'],
        $input['winners']
    ]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Result uploaded successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save result']);
    }
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $exception->getMessage()]);
} catch(Exception $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $exception->getMessage()]);
}
?>