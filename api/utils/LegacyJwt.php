<?php

class LegacyJwt
{
    private static $secretKey = null;
    private const ALGORITHM = 'HS256';

    public static function decode($jwt)
    {
        $tokenParts = explode('.', (string) $jwt);

        if (count($tokenParts) !== 3) {
            return false;
        }

        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        $signature = $tokenParts[2];

        $expectedSignature = hash_hmac(
            'sha256',
            $tokenParts[0] . "." . $tokenParts[1],
            self::getSecretKey(),
            true
        );
        $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }

        return json_decode($payload, true);
    }

    private static function getSecretKey()
    {
        if (self::$secretKey !== null) {
            return self::$secretKey;
        }

        $envFile = dirname(__DIR__) . '/.env';

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (strpos($line, '=') === false || strpos($line, '#') === 0) {
                    continue;
                }

                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }

        self::$secretKey = $_ENV['JWT_SECRET'] ?? 'L8k9mN2pQ5rS7tU1vW3xY6zA4bC8dE0fG2hI5jK7lM9nO1pQ4rS6tU8vW0xY3zA5';

        return self::$secretKey;
    }
}
