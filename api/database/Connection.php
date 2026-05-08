<?php

class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            
            $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']};port={$config['port']}";

            self::$instance = new PDO(
                $dsn, 
                $config['username'], 
                $config['password'], 
                $config['options']
            );
        }

        return self::$instance;
    }

    public static function getConfig(): array
    {
        return require __DIR__ . '/../config/database.php';
    }

    public static function test(): bool
    {
        try {
            $pdo = self::get();
            $pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}