<?php

return [
    'host' => Env::get('DB_HOST', 'localhost'),
    'name' => Env::get('DB_NAME'),
    'username' => Env::get('DB_USERNAME'),
    'password' => Env::get('DB_PASSWORD'),
    'port' => Env::get('DB_PORT', 3306),
    'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4, time_zone = '" . date('P') . "'"
    ]
];