<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $pools = [
        'Monday Lotto' => 10,
        'Wednesday Lotto' => 10,
        'Friday Lotto' => 10
    ];
    
    $query = "SELECT lottery, COUNT(*) * 0.01 as pool_amount 
              FROM entries 
              GROUP BY lottery";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $poolData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($poolData as $row) {
        $lottery = $row['lottery'];
        // Map old names to new names
        if ($lottery === 'Mon Lotto') $lottery = 'Monday Lotto';
        if ($lottery === 'Wed Lotto') $lottery = 'Wednesday Lotto';
        if ($lottery === 'Fri Lotto') $lottery = 'Friday Lotto';
        
        if (isset($pools[$lottery])) {
            $pools[$lottery] += floatval($row['pool_amount']);
        }
    }
    
    echo json_encode($pools);
    
} catch(PDOException $exception) {
    echo json_encode(['Monday Lotto' => 10, 'Wednesday Lotto' => 10, 'Friday Lotto' => 10]);
}
?>