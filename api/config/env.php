<?php

class Env
{
    private static $variables = [];
    private static $loaded = false;

    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        $envFile = $path ?: dirname(__DIR__) . '/.env';
        
        if (!file_exists($envFile)) {
            throw new Exception(".env file not found at: " . $envFile);
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                $value = trim($value, '"\'');
                
                $_ENV[$key] = $value;
                self::$variables[$key] = $value;
                
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        return $default;
    }

    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset($_ENV[$key]) || isset($_SERVER[$key]) || isset(self::$variables[$key]);
    }

    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return array_merge($_ENV, self::$variables);
    }

    public static function set($key, $value)
    {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        self::$variables[$key] = $value;
    }

    public static function database()
    {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'name' => self::get('DB_NAME'),
            'username' => self::get('DB_USERNAME'),
            'password' => self::get('DB_PASSWORD'),
            'port' => self::get('DB_PORT', 3306),
            'charset' => self::get('DB_CHARSET', 'utf8mb4')
        ];
    }

    public static function app()
    {
        return [
            'env' => self::get('APP_ENV', 'development'),
            'debug' => self::get('APP_DEBUG', 'true') === 'true',
            'url' => self::get('APP_URL', 'http://localhost'),
            'timezone' => self::get('TIMEZONE', 'UTC')
        ];
    }

    public static function isDebug()
    {
        return self::get('APP_DEBUG', 'true') === 'true';
    }

    public static function isProduction()
    {
        return self::get('APP_ENV', 'development') === 'production';
    }

    public static function isDevelopment()
    {
        return self::get('APP_ENV', 'development') === 'development';
    }
}

Env::load();