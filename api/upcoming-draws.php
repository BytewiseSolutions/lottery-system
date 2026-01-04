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

try {
    // Create upcoming_draws table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS upcoming_draws (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lottery VARCHAR(50) NOT NULL,
        draw_date DATETIME NOT NULL,
        jackpot VARCHAR(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($createTable);
    
    // Add sample data if table is empty
    $checkData = "SELECT COUNT(*) FROM upcoming_draws";
    $stmt = $db->prepare($checkData);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $sampleData = [
            ['Monday Lotto', date('Y-m-d H:i:s', strtotime('next Monday 20:00')), '$10.00'],
            ['Wednesday Lotto', date('Y-m-d H:i:s', strtotime('next Wednesday 20:00')), '$10.00'],
            ['Friday Lotto', date('Y-m-d H:i:s', strtotime('next Friday 20:00')), '$10.00']
        ];
        
        $insertStmt = $db->prepare("INSERT INTO upcoming_draws (lottery, draw_date, jackpot) VALUES (?, ?, ?)");
        foreach ($sampleData as $data) {
            $insertStmt->execute($data);
        }
    }
    
    // Auto-update past draws to next week
    $updatePastDraws = "UPDATE upcoming_draws SET draw_date = CASE 
        WHEN lottery = 'Monday Lotto' THEN DATE_ADD(CURDATE(), INTERVAL (7 - WEEKDAY(CURDATE()) + 0) DAY) + INTERVAL '20:00:00' HOUR_SECOND
        WHEN lottery = 'Wednesday Lotto' THEN DATE_ADD(CURDATE(), INTERVAL (7 - WEEKDAY(CURDATE()) + 2) DAY) + INTERVAL '20:00:00' HOUR_SECOND
        WHEN lottery = 'Friday Lotto' THEN DATE_ADD(CURDATE(), INTERVAL (7 - WEEKDAY(CURDATE()) + 4) DAY) + INTERVAL '20:00:00' HOUR_SECOND
    END WHERE draw_date < NOW()";
    $db->exec($updatePastDraws);
    
    // Get upcoming draws from database
    $query = "SELECT * FROM upcoming_draws WHERE draw_date >= NOW() ORDER BY draw_date ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format draws for frontend
    $formattedDraws = [];
    foreach ($draws as $draw) {
        $formattedDraws[] = [
            'id' => $draw['id'],
            'lottery' => $draw['lottery'],
            'drawDate' => $draw['draw_date'],
            'jackpot' => $draw['jackpot'],
            'status' => $draw['status'] ?? 'scheduled'
        ];
    }
    
    echo json_encode($formattedDraws);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch upcoming draws']);
}
?>