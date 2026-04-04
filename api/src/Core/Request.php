<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private ?array $jsonBody = null;

    public function __construct(
        private readonly string $method,
        private readonly array $query,
        private readonly array $server
    ) {}

    public static function capture(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_GET,
            $_SERVER
        );
    }

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function ensureMethod(string ...$allowedMethods): void
    {
        $allowedMethods = array_map('strtoupper', $allowedMethods);
        if (!in_array($this->method(), $allowedMethods, true)) {
            throw new ApiException('Method not allowed', 405);
        }
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function body(): array
    {
        if ($this->jsonBody !== null) {
            return $this->jsonBody;
        }

        $rawBody = file_get_contents('php://input');
        $decoded = json_decode($rawBody ?: '[]', true);
        $this->jsonBody = is_array($decoded) ? $decoded : [];

        return $this->jsonBody;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }
}
