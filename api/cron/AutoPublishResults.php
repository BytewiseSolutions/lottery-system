<?php

require_once __DIR__ . '/../config/bootstrap.php';

$service = new ResultService();
$result = $service->autoPublishDueResults();

if (!$result['success']) {
    echo "Auto publish failed: " . ($result['message'] ?? 'Unknown error') . PHP_EOL;
    exit(1);
}

$data = $result['data'] ?? [];

echo "Auto publish complete" . PHP_EOL;
echo "Processed: " . ($data['processed_count'] ?? 0) . PHP_EOL;
echo "Skipped: " . ($data['skipped_count'] ?? 0) . PHP_EOL;

foreach (($data['processed'] ?? []) as $item) {
    echo "[processed] Draw {$item['draw_id']} ({$item['lottery']}) at {$item['draw_date']}" . PHP_EOL;
}

foreach (($data['skipped'] ?? []) as $item) {
    echo "[skipped] Draw {$item['draw_id']} ({$item['lottery']}) at {$item['draw_date']} - {$item['reason']}" . PHP_EOL;
}
