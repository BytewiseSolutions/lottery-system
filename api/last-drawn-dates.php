<?php
require_once 'config/database.php';
require_once 'config/cors.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $db = (new Database())->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    $numbers = $input['numbers'] ?? [];
    $lottery = $input['lottery'] ?? '';
    
    if (empty($numbers) || !is_array($numbers)) {
        http_response_code(400);
        echo json_encode(['error' => 'Numbers array is required']);
        exit;
    }
    
    $lastDrawnDates = [];
    
    foreach ($numbers as $number) {
        // Find the most recent draw where this number appeared
        $stmt = $db->prepare("
            SELECT draw_date, lottery, winning_numbers, bonus_numbers 
            FROM lottery_result 
            WHERE lottery LIKE ? 
            AND (
                JSON_CONTAINS(winning_numbers, ?) 
                OR JSON_CONTAINS(bonus_numbers, ?)
            )
            ORDER BY draw_date DESC 
            LIMIT 1
        ");
        
        $lotteryPattern = '%' . $lottery . '%';
        $numberJson = json_encode($number);
        
        $stmt->execute([$lotteryPattern, $numberJson, $numberJson]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $lastDrawnDates[$number] = [
                'number' => $number,
                'last_drawn' => $result['draw_date'],
                'lottery' => $result['lottery'],
                'in_winning' => json_decode($result['winning_numbers'], true) && in_array($number, json_decode($result['winning_numbers'], true)),
                'in_bonus' => json_decode($result['bonus_numbers'], true) && in_array($number, json_decode($result['bonus_numbers'], true))
            ];
        } else {
            $lastDrawnDates[$number] = [
                'number' => $number,
                'last_drawn' => null,
                'lottery' => null,
                'in_winning' => false,
                'in_bonus' => false
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'last_drawn_dates' => $lastDrawnDates
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>