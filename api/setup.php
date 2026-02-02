<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed. Check your .env file.\n");
}

try {
    // Read and execute setup SQL
    $sql = file_get_contents(__DIR__ . '/setup-database.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        $db->exec($statement);
    }
    
    echo "Database setup completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Setup failed: " . $e->getMessage() . "\n";
}
?>
