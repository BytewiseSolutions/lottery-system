<?php

class CorsMiddleware
{
    private static $allowedOrigins = [
        'https://www.totalfreelotto.com',
        'https://totalfreelotto.com',
        'http://localhost:4200',
    ];

    private static function isAllowed(string $origin): bool
    {
        if (in_array($origin, self::$allowedOrigins)) {
            return true;
        }
        // Allow any localhost port for local development
        return (bool) preg_match('/^http:\/\/localhost:\d+$/', $origin);
    }

    public static function handle()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (self::isAllowed($origin)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
        }

        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Max-Age: 3600");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}