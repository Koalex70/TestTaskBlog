<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\NotFoundController;
use App\Http\Response;

final class SlugResourceResolver
{
    /**
     * @param array<string, string> $params
     * @param callable(string): ?array<string, mixed> $finder
     * @param null|callable(): Response $notFoundResponseFactory
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

            return (new NotFoundController())->show();
        }

        return $resource;
    }
}
