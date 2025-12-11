<?php
class JWT {
    private static $secret_key;
    private static $algorithm = 'HS256';

    private static function getSecretKey() {
        if (!self::$secret_key) {
            // Load .env file
            $envFile = __DIR__ . '/../.env';
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                        list($key, $value) = explode('=', $line, 2);
                        $_ENV[trim($key)] = trim($value);
                    }
                }
            }
            self::$secret_key = $_ENV['JWT_SECRET'] ?? 'L8k9mN2pQ5rS7tU1vW3xY6zA4bC8dE0fG2hI5jK7lM9nO1pQ4rS6tU8vW0xY3zA5';
        }
        return self::$secret_key;
    }

    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        $payload = json_encode($payload);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::getSecretKey(), true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    public static function decode($jwt) {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) != 3) {
            return false;
        }

        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        $signature = $tokenParts[2];

        $expectedSignature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], self::getSecretKey(), true);
        $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if ($signature !== $expectedSignature) {
            return false;
        }

        return json_decode($payload, true);
    }

    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Access token required']);
            exit;
        }

        $token = $matches[1];
        $decoded = self::decode($token);
        
        if (!$decoded) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }

        return $decoded;
    }
}
?>