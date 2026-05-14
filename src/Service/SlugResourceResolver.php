<?php

declare(strict_types=1);

namespace App\Service;

use App\Http\Response;
use Closure;

final class SlugResourceResolver
{
    public function __construct(
        private readonly Closure $htmlNotFoundResponseFactory
    ) {
    }

    /**
     * @param array<string, string> $params
     * @param callable(string): ?array<string, mixed> $finder
     * @param null|callable(): Response $notFoundResponseFactory Overrides HTML 404 when set
     *
     * @return array<string, mixed>|Response
     */
    public function resolveOrNotFound(
        array $params,
        callable $finder,
        ?callable $notFoundResponseFactory = null
    ): array|Response {
        $slug = $params['slug'] ?? '';
        $resource = $finder($slug);

        if ($resource === null) {
            if ($notFoundResponseFactory !== null) {
                return $notFoundResponseFactory();
            }

            return ($this->htmlNotFoundResponseFactory)();
        }

        return $resource;
    }
}
