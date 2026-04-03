<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $now = new DateTime();
    
    $stmt = $db->prepare("
        SELECT id, lottery, draw_date, jackpot, status 
        FROM upcoming_draw 
        WHERE status = 'scheduled' 
        ORDER BY draw_date ASC
    ");
    $stmt->execute();
    $draws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $currentVotingDraw = null;
    
    foreach ($draws as $draw) {
        $drawDateTime = new DateTime($draw['draw_date']);
        
        $votingCloseTime = clone $drawDateTime;
        $votingCloseTime->setTime(19, 59, 59);
        
        if ($now <= $votingCloseTime) {
            $currentVotingDraw = [
                'id' => $draw['id'],
                'lottery' => $draw['lottery'],
                'lottery_type' => $draw['lottery'], 
                'draw_date' => $draw['draw_date'],
                'jackpot' => $draw['jackpot'],
                'status' => $draw['status'],
                'voting_closes_at' => $votingCloseTime->format('Y-m-d H:i:s'),
                'is_voting_open' => true
            ];
            break;
        }
    }
    
    if (!$currentVotingDraw && !empty($draws)) {
        $nextDraw = $draws[0];
        $drawDateTime = new DateTime($nextDraw['draw_date']);
        $votingCloseTime = clone $drawDateTime;
        $votingCloseTime->setTime(19, 59, 59);
        
        $currentVotingDraw = [
            'id' => $nextDraw['id'],
            'lottery' => $nextDraw['lottery'],
            'lottery_type' => $nextDraw['lottery'],
            'draw_date' => $nextDraw['draw_date'],
            'jackpot' => $nextDraw['jackpot'],
            'status' => $nextDraw['status'],
            'voting_closes_at' => $votingCloseTime->format('Y-m-d H:i:s'),
            'is_voting_open' => $now <= $votingCloseTime
        ];
    }
    
    if ($currentVotingDraw) {
        echo json_encode([
            'success' => true,
            'current_voting_draw' => $currentVotingDraw,
            'server_time' => $now->format('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No voting draws available',
            'server_time' => $now->format('Y-m-d H:i:s')
        ]);
    }
    
} catch (Exception $e) {
    error_log("Current voting draw error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>