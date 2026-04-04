<?php
declare(strict_types=1);

require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/jwt.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativePath = substr($class, strlen($prefix));
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relativePath) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

function api_handle(callable $handler): void
{
    try {
        $response = $handler(App\Core\Request::capture());
        App\Core\JsonResponse::send($response);
    } catch (App\Core\ApiException $exception) {
        App\Core\JsonResponse::send(
            ['error' => $exception->getMessage()],
            $exception->getStatusCode()
        );
    } catch (Throwable $exception) {
        error_log('Unhandled API error: ' . $exception->getMessage());
        App\Core\JsonResponse::send(['error' => 'Internal server error'], 500);
    }
}
