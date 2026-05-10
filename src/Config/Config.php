<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class Config
{
    /** @var array<string, array<string, mixed>> */
    private static array $cache = [];

    public static function get(string $namespace, string $key, mixed $default = null): mixed
    {
        $config = self::namespace($namespace);

        return $config[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public static function namespace(string $namespace): array
    {
        if (isset(self::$cache[$namespace])) {
            return self::$cache[$namespace];
        }

        $filePath = dirname(__DIR__, 2) . '/config/' . $namespace . '.php';
        if (!is_file($filePath)) {
            throw new RuntimeException(sprintf('Config file "%s" not found.', $filePath));
        }

        $config = require $filePath;
        if (!is_array($config)) {
            throw new RuntimeException(sprintf('Config "%s" must return an array.', $namespace));
        }

        self::$cache[$namespace] = $config;

        return self::$cache[$namespace];
    }
}
