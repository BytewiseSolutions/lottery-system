<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\Request;
use App\Domain\Voting\UpcomingDrawRepository;
use App\Infrastructure\Database\DatabaseConnection;

final class VotingCountdownController
{
    public function __invoke(Request $request): array
    {
        $request->ensureMethod('GET');

        $service = new VotingScheduleService(
            new UpcomingDrawRepository((new DatabaseConnection())->pdo())
        );

        return $service->countdown();
    }
}
