<?php
set_time_limit(0);
date_default_timezone_set('Africa/Johannesburg');

echo "Development scheduler started. Press Ctrl+C to stop.\n";

$lastDrawGenerationHour = null;

while (true) {
    $now = date('Y-m-d H:i:s');
    echo "[{$now}] Syncing draft results...\n";
    include __DIR__ . '/cron-sync-draft-results.php';

    $currentHour = date('Y-m-d H');
    if ($currentHour !== $lastDrawGenerationHour) {
        echo "[{$now}] Running draw generation...\n";
        include __DIR__ . '/cron-generate-draws.php';
        $lastDrawGenerationHour = $currentHour;
    }

    sleep(60);
}
