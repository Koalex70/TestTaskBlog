<?php

declare(strict_types=1);

namespace App\Service;

final class EnvironmentService
{
    public function current(): string
    {
        return $_ENV['APP_ENV'] ?? 'prod';
    }

    public function isDev(): bool
    {
        return $this->current() === 'dev';
    }
}
