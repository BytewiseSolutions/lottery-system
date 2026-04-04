<?php
declare(strict_types=1);

namespace App\Application\Stats;

use App\Domain\Stats\StatsRepository;
use Throwable;

final class StatsService
{
    public function __construct(private readonly StatsRepository $stats) {}

    public function summary(): array
    {
        try {
            return $this->stats->fetchSummary();
        } catch (Throwable $exception) {
            error_log('Stats service error: ' . $exception->getMessage());

            return [
                'totalUsers' => 0,
                'winnersLastMonth' => 0,
                'totalEntries' => 0,
                'totalPayouts' => 0,
            ];
        }
    }
}
