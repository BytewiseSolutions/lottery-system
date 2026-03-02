<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT id, lottery, numbers, bonus_numbers, vote_date, created_at FROM vote WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$votes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($votes as $vote) {
    $result[] = [
        'id' => $vote['id'],
        'lottery' => $vote['lottery'],
        'numbers' => json_decode($vote['numbers']),
        'bonusNumbers' => json_decode($vote['bonus_numbers']),
        'voteDate' => $vote['vote_date'],
        'createdAt' => $vote['created_at']
    ];
}

echo json_encode(['votes' => $result]);
?>
