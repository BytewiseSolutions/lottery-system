<?php
declare(strict_types=1);

namespace App\Application\Stats;

use App\Core\Request;
use App\Domain\Stats\StatsRepository;
use App\Infrastructure\Database\DatabaseConnection;

final class StatsController
{
    public function __invoke(Request $request): array
    {
        $request->ensureMethod('GET');

        return (new StatsService(
            new StatsRepository((new DatabaseConnection())->pdo())
        ))->summary();
    }
}
