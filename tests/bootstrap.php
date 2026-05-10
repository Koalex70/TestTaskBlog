<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$projectRoot = dirname(__DIR__);
$envPath = $projectRoot . '/.env';
if (is_file($envPath)) {
    Dotenv::createImmutable($projectRoot)->safeLoad();
}

$dbHost = (string) ($_ENV['DB_HOST'] ?? 'mysql');
$dbPort = (int) ($_ENV['DB_PORT'] ?? 3306);
$dbCharset = (string) ($_ENV['DB_CHARSET'] ?? 'utf8mb4');
$dbCollation = (string) ($_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci');
$testDatabase = (string) ($_ENV['DB_TEST_DATABASE'] ?? 'blog_test');
$dbRootPassword = (string) ($_ENV['DB_ROOT_PASSWORD'] ?? 'root_password');
$testUsername = (string) ($_ENV['DB_TEST_USERNAME'] ?? 'root');
$testPassword = (string) ($_ENV['DB_TEST_PASSWORD'] ?? $dbRootPassword);

$rootDsn = sprintf('mysql:host=%s;port=%d;charset=%s', $dbHost, $dbPort, $dbCharset);
$rootPdo = new PDO($rootDsn, 'root', $dbRootPassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$rootPdo->exec(
    sprintf(
        'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s',
        str_replace('`', '``', $testDatabase),
        $dbCharset,
        $dbCollation
    )
);

$_ENV['DB_HOST'] = $dbHost;
$_ENV['DB_PORT'] = (string) $dbPort;
$_ENV['DB_DATABASE'] = $testDatabase;
$_ENV['DB_USERNAME'] = $testUsername;
$_ENV['DB_PASSWORD'] = $testPassword;

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');
