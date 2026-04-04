<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\Request;

final class QuickPickController
{
    public function __invoke(Request $request): array
    {
        $request->ensureMethod('POST');

        return (new QuickPickService())->generate($request->body());
    }
}
