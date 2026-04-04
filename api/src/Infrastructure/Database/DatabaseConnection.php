<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Core\ApiException;
use PDO;

final class DatabaseConnection
{
    public function pdo(): PDO
    {
        $database = new \Database();
        $connection = $database->getConnection();

        if (!$connection instanceof PDO) {
            throw new ApiException('Database connection failed', 500);
        }

        return $connection;
    }
}
