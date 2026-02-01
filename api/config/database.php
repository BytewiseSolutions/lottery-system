<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $driver;
    private $db_path;
    public $conn;

    public function __construct() {
        $this->loadEnv();
        
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'lottery_db';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->driver = $_ENV['DB_DRIVER'] ?? 'mysql';
        $this->db_path = $_ENV['DB_PATH'] ?? '';
    }
    
    private function loadEnv() {
        // Check for local environment file first
        $envFile = __DIR__ . '/../.env.local';
        if (!file_exists($envFile)) {
            $envFile = __DIR__ . '/../.env';
        }
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
    }

    public function getConnection() {
        $this->conn = null;
        try {
            if ($this->driver === 'sqlite') {
                $dbPath = __DIR__ . '/../' . $this->db_path;
                $this->conn = new PDO(
                    "sqlite:" . $dbPath,
                    null,
                    null,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } else {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                $this->conn->exec("SET time_zone = '+02:00'");
            }
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            // Don't echo here - let the calling script handle the error
        }
        return $this->conn;
    }
}
?>