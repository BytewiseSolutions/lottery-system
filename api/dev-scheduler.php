<?php
set_time_limit(0);
date_default_timezone_set('Africa/Johannesburg');

echo "Development scheduler started. Press Ctrl+C to stop.\n";

while (true) {
    echo "[" . date('Y-m-d H:i:s') . "] Running draw generation...\n";
    
    include __DIR__ . '/cron-generate-draws.php';
    
    sleep(3600); // Run every hour
}
