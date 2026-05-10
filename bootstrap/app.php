<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$projectRoot = dirname(__DIR__);
$envPath = $projectRoot . '/.env';

if (is_file($envPath)) {
    Dotenv::createImmutable($projectRoot)->safeLoad();
}

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
