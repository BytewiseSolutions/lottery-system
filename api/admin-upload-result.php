<?php
require_once 'config/cors.php';
require_once 'config/database.php';

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
    $required = ['lottery', 'drawDate', 'jackpot', 'winners'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing field: $field"]);
            exit;
        }
    }
    
    // Get numbers from input (filter out zeros)
    $numbers = array_filter($input['numbers'] ?? [], function($n) { return $n > 0; });
    $bonusNumbers = array_filter($input['bonusNumbers'] ?? [], function($n) { return $n > 0; });
    
    if (count($numbers) < 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Must have at least 3 winning numbers']);
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
        status VARCHAR(20) DEFAULT 'published',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($createTable);
    
    // Format jackpot
    $jackpot = '$' . number_format(floatval($input['jackpot']), 2);
    
    // Insert result
    $query = "INSERT INTO results (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    $status = $input['publishNow'] ? 'published' : 'draft';
    
    $success = $stmt->execute([
        $input['lottery'],
        date('Y-m-d', strtotime($input['drawDate'])),
        json_encode(array_values($numbers)),
        json_encode(array_values($bonusNumbers)),
        $jackpot,
        $input['winners'],
        $status,
        $input['notes'] ?? ''
    ]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Result uploaded successfully', 'id' => $db->lastInsertId()]);
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