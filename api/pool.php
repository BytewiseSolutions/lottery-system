<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT lottery, COUNT(*) * 0.001 as pool_amount 
              FROM entries 
              GROUP BY lottery";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $poolData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pools = [
        'Monday Lotto' => 0,
        'Wednesday Lotto' => 0,
        'Friday Lotto' => 0
    ];
    
    foreach ($poolData as $row) {
        $pools[$row['lottery']] = $row['pool_amount'];
    }
    
    echo json_encode($pools);
    
} catch(PDOException $exception) {
    echo json_encode(['Monday Lotto' => 0, 'Wednesday Lotto' => 0, 'Friday Lotto' => 0]);
}
?>