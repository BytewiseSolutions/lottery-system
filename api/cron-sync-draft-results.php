<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$db = (new App\Infrastructure\Database\DatabaseConnection())->pdo();
$service = new App\Application\Results\AutoDraftResultService($db);
$result = $service->syncEligibleDrafts();

if (PHP_SAPI === 'cli') {
    echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    return;
}

App\Core\JsonResponse::send($result);
