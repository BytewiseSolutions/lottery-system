<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/constants.php';

date_default_timezone_set(Env::get('TIMEZONE', 'Africa/Maseru'));

require_once __DIR__ . '/autoload.php';

DefaultAdminBootstrap::ensure();
