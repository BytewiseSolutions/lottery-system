<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "Database connection failed\n";
    exit(1);
}

try {
    echo "Updating lottery names from abbreviated to full day names...\n";
    
    // Update entries table
    $stmt = $db->prepare("UPDATE entries SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto'");
    $stmt->execute();
    echo "Updated entries: Mon Lotto -> Monday Lotto\n";
    
    $stmt = $db->prepare("UPDATE entries SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto'");
    $stmt->execute();
    echo "Updated entries: Wed Lotto -> Wednesday Lotto\n";
    
    $stmt = $db->prepare("UPDATE entries SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto'");
    $stmt->execute();
    echo "Updated entries: Fri Lotto -> Friday Lotto\n";
    
    // Update results table
    $stmt = $db->prepare("UPDATE results SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto'");
    $stmt->execute();
    echo "Updated results: Mon Lotto -> Monday Lotto\n";
    
    $stmt = $db->prepare("UPDATE results SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto'");
    $stmt->execute();
    echo "Updated results: Wed Lotto -> Wednesday Lotto\n";
    
    $stmt = $db->prepare("UPDATE results SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto'");
    $stmt->execute();
    echo "Updated results: Fri Lotto -> Friday Lotto\n";
    
    // Update winners table
    $stmt = $db->prepare("UPDATE winners SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto'");
    $stmt->execute();
    echo "Updated winners: Mon Lotto -> Monday Lotto\n";
    
    $stmt = $db->prepare("UPDATE winners SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto'");
    $stmt->execute();
    echo "Updated winners: Wed Lotto -> Wednesday Lotto\n";
    
    $stmt = $db->prepare("UPDATE winners SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto'");
    $stmt->execute();
    echo "Updated winners: Fri Lotto -> Friday Lotto\n";
    
    echo "All lottery names updated successfully!\n";
    
} catch(PDOException $exception) {
    echo "Error updating lottery names: " . $exception->getMessage() . "\n";
    exit(1);
}
?>