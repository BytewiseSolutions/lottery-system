<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing notification endpoint...<br><br>";

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT n.*, COALESCE(u.full_name, 'System') as sent_by_name 
              FROM notification n 
              LEFT JOIN user u ON n.sent_by = u.id 
              ORDER BY n.created_at DESC 
              LIMIT 5";
    
    $stmt = $db->query($query);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($notifications) . " notifications<br><br>";
    
    foreach ($notifications as $notif) {
        echo "<strong>Notification #" . $notif['id'] . ":</strong><br>";
        foreach ($notif as $key => $value) {
            echo "&nbsp;&nbsp;" . $key . ": " . ($value ?? 'NULL') . "<br>";
        }
        echo "<br>";
    }
    
    echo "<br><strong>JSON Output:</strong><br>";
    echo "<pre>" . json_encode($notifications, JSON_PRETTY_PRINT) . "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
