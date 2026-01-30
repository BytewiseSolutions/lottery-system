<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Updating winners table schema...\n";
    
    $sql = file_get_contents(__DIR__ . '/fix-winners-table.sql');
    $db->exec($sql);
    
    echo "Winners table updated successfully!\n";
    echo "The following columns were added:\n";
    echo "- result_id (INT)\n";
    echo "- entry_id (INT)\n";
    echo "- status (VARCHAR(20), default 'pending')\n";
    echo "- draw_date (DATE)\n";
    echo "- claimed_at (TIMESTAMP)\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
