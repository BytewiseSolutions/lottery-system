<?php
/**
 * Database Setup Script for Hostinger
 * Upload this file to your server and run it once to setup the database
 * DELETE THIS FILE AFTER RUNNING!
 */

require_once 'config/database.php';

echo "<h2>Database Setup for Lottery System</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die("❌ Database connection failed! Check your .env file.");
    }
    
    echo "✅ Database connected successfully!<br><br>";
    
    // Read and execute init-db-updated.sql
    $initSql = file_get_contents('init-db-updated.sql');
    if ($initSql) {
        $statements = explode(';', $initSql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $db->exec($statement);
            }
        }
        echo "✅ Database schema created successfully!<br>";
    }
    
    // Read and execute security-updates.sql
    $securitySql = file_get_contents('security-updates.sql');
    if ($securitySql) {
        $statements = explode(';', $securitySql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $db->exec($statement);
                } catch (Exception $e) {
                    // Ignore duplicate entry errors for admin user
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        throw $e;
                    }
                }
            }
        }
        echo "✅ Admin user and security updates applied!<br>";
    }
    
    echo "<br><strong>🎉 Database setup completed successfully!</strong><br>";
    echo "<br><strong>⚠️ IMPORTANT: Delete this setup.php file now for security!</strong><br>";
    echo "<br>Admin Login:<br>";
    echo "Email: admin@totalfreelotto.com<br>";
    echo "Password: Admin@2026!<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>