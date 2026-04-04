<?php
declare(strict_types=1);

namespace App\Infrastructure\Auth;

final class JwtAuthenticator
{
    public function authenticate(): array
    {
        return \JWT::authenticate();
    }
}
