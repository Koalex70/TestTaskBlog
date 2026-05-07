<?php

declare(strict_types=1);

namespace App\Http;

final class Response
{
    public function __construct(
        private readonly string $content,
        private readonly int $statusCode = 200
    ) {
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        echo $this->content;
    }
}
