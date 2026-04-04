<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\Request;
use App\Domain\Voting\AdminVoteRepository;
use App\Domain\Voting\VoteRepository;
use App\Infrastructure\Database\DatabaseConnection;

final class LeadingNumbersController
{
    public function __invoke(Request $request): array
    {
        $request->ensureMethod('GET');

        $db = (new DatabaseConnection())->pdo();
        $service = new LeadingNumbersService(
            new VoteRepository($db),
            new AdminVoteRepository($db)
        );

        return $service->summary(
            (string) $request->query('lottery', ''),
            (string) $request->query('voteDate', date('Y-m-d'))
        );
    }
}
