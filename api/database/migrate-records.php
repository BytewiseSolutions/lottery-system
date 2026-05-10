<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';

/**
 * Data-only migration script.
 *
 * Default behavior:
 * - reads DB connection details from api/.env
 * - truncates target tables
 * - copies records from source DB into target DB
 *
 * Usage:
 *   php api/database/migrate-records.php
 *   php api/database/migrate-records.php --source=u606331557_lottery_db --target=u606331557_lottery_system
 *   php api/database/migrate-records.php --host=auth-db497.hstgr.io --username=... --password=...
 *   php api/database/migrate-records.php --no-truncate
 */

$defaultSourceDatabase = 'u606331557_lottery_db';
$defaultTargetDatabase = 'u606331557_lottery_system';

$options = getopt('', [
    'host::',
    'port::',
    'username::',
    'password::',
    'charset::',
    'source::',
    'target::',
    'no-truncate'
]);

$sourceDatabase = (string)($options['source'] ?? $defaultSourceDatabase);
$targetDatabase = (string)($options['target'] ?? $defaultTargetDatabase);
$truncateTarget = !isset($options['no-truncate']);

validateDatabaseName($sourceDatabase, 'source');
validateDatabaseName($targetDatabase, 'target');

if ($sourceDatabase === $targetDatabase) {
    fwrite(STDERR, "Source and target databases must be different.\n");
    exit(1);
}

$config = Env::database();

$host = (string)($options['host'] ?? $config['host'] ?? 'localhost');
$port = (string)($options['port'] ?? $config['port'] ?? '3306');
$username = (string)($options['username'] ?? $config['username'] ?? '');
$password = (string)($options['password'] ?? $config['password'] ?? '');
$charset = (string)($options['charset'] ?? $config['charset'] ?? 'utf8mb4');

if ($host === '' || $username === '') {
    fwrite(STDERR, "Database connection settings are missing. Provide them in api/.env or via --host and --username.\n");
    exit(1);
}

$dsn = sprintf(
    'mysql:host=%s;port=%s;charset=%s',
    $host,
    $port ?: '3306',
    $charset ?: 'utf8mb4'
);

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    assertDatabaseExists($pdo, $sourceDatabase);
    assertDatabaseExists($pdo, $targetDatabase);

    $orderedTables = [
        'user',
        'lottery',
        'user_tokens',
        'password_resets',
        'password_reset',
        'data_file',
        'activity_log',
        'draw',
        'vote',
        'admin_vote',
        'entry',
        'result',
        'winner',
        'payment',
        'notification',
        'contact_message',
        'system_setting',
        'highest_vote',
    ];

    $sourceTables = getTables($pdo, $sourceDatabase);
    $targetTables = getTables($pdo, $targetDatabase);
    $commonTables = array_values(array_intersect($orderedTables, $sourceTables, $targetTables));

    if (empty($commonTables)) {
        throw new RuntimeException('No matching tables were found between source and target databases.');
    }

    echo "Starting record migration\n";
    echo "Host: {$host}:{$port}\n";
    echo "Source: {$sourceDatabase}\n";
    echo "Target: {$targetDatabase}\n";
    echo "Mode: " . ($truncateTarget ? 'truncate and copy' : 'append only') . "\n\n";

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    if ($truncateTarget) {
        foreach (array_reverse($commonTables) as $table) {
            $pdo->exec(sprintf('TRUNCATE TABLE %s', quoteIdentifier($targetDatabase, $table)));
            echo "Truncated {$targetDatabase}.{$table}\n";
        }

        echo "\n";
    }

    foreach ($commonTables as $table) {
        $columns = getSharedColumns($pdo, $sourceDatabase, $targetDatabase, $table);

        if (empty($columns)) {
            echo "Skipped {$table}: no shared columns found\n";
            continue;
        }

        $quotedColumns = implode(', ', array_map(static fn(string $column): string => quoteIdentifier($column), $columns));
        $targetTable = quoteIdentifier($targetDatabase, $table);
        $sourceTable = quoteIdentifier($sourceDatabase, $table);

        $insertSql = sprintf(
            'INSERT INTO %s (%s) SELECT %s FROM %s',
            $targetTable,
            $quotedColumns,
            $quotedColumns,
            $sourceTable
        );

        $rowCount = (int)$pdo->exec($insertSql);
        echo sprintf("Copied %d record(s) into %s.%s\n", $rowCount, $targetDatabase, $table);
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "\nMigration completed successfully.\n";
    exit(0);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        } catch (Throwable $ignored) {
        }
    }

    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}

function validateDatabaseName(string $databaseName, string $label): void
{
    if ($databaseName === '' || !preg_match('/^[A-Za-z0-9_]+$/', $databaseName)) {
        throw new InvalidArgumentException("Invalid {$label} database name: {$databaseName}");
    }
}

function assertDatabaseExists(PDO $pdo, string $databaseName): void
{
    $statement = $pdo->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :database_name');
    $statement->execute([':database_name' => $databaseName]);

    if (!$statement->fetchColumn()) {
        throw new RuntimeException("Database not found: {$databaseName}");
    }
}

function getTables(PDO $pdo, string $databaseName): array
{
    $statement = $pdo->query(sprintf('SHOW TABLES FROM %s', quoteIdentifier($databaseName)));
    $tables = [];

    foreach ($statement->fetchAll(PDO::FETCH_NUM) as $row) {
        $tables[] = (string)$row[0];
    }

    return $tables;
}

function getSharedColumns(PDO $pdo, string $sourceDatabase, string $targetDatabase, string $tableName): array
{
    $sourceColumns = getTableColumns($pdo, $sourceDatabase, $tableName);
    $targetColumns = getTableColumns($pdo, $targetDatabase, $tableName);

    return array_values(array_intersect($targetColumns, $sourceColumns));
}

function getTableColumns(PDO $pdo, string $databaseName, string $tableName): array
{
    $statement = $pdo->prepare(
        'SELECT COLUMN_NAME
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = :database_name
           AND TABLE_NAME = :table_name
         ORDER BY ORDINAL_POSITION'
    );
    $statement->execute([
        ':database_name' => $databaseName,
        ':table_name' => $tableName
    ]);

    return array_map(
        static fn(array $row): string => (string)$row['COLUMN_NAME'],
        $statement->fetchAll(PDO::FETCH_ASSOC)
    );
}

function quoteIdentifier(string ...$parts): string
{
    return implode('.', array_map(
        static fn(string $part): string => '`' . str_replace('`', '``', $part) . '`',
        $parts
    ));
}
