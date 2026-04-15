<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\Request;
use App\Domain\Voting\AdminVoteRepository;
use App\Domain\Voting\LeadingNumbersSnapshotRepository;
use App\Domain\Voting\VoteRepository;
use App\Infrastructure\Auth\JwtAuthenticator;
use App\Infrastructure\Database\DatabaseConnection;

final class VoteController
{
    public function __invoke(Request $request): array
    {
        $db = (new DatabaseConnection())->pdo();
        $service = new VoteService(
            new VoteRepository($db),
            new LeadingNumbersService(
                new VoteRepository($db),
                new AdminVoteRepository($db),
                new LeadingNumbersSnapshotRepository($db)
            )
        );
        $user = (new JwtAuthenticator())->authenticate();

        if ($request->method() === 'POST') {
            return $service->submit((int) $user['id'], $request->body());
        }

        if ($request->method() === 'GET') {
            return $service->history((int) $user['id']);
        }

        $request->ensureMethod('GET', 'POST');

        return [];
    }
}
