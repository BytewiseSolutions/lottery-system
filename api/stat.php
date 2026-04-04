<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

api_handle(new App\Application\Stats\StatsController());
