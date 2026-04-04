<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\Request;
use App\Infrastructure\Auth\JwtAuthenticator;
use App\Infrastructure\Database\DatabaseConnection;
use App\Domain\Voting\VoteRepository;

final class VoteController
{
    public function __invoke(Request $request): array
    {
        $service = new VoteService(new VoteRepository((new DatabaseConnection())->pdo()));
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
