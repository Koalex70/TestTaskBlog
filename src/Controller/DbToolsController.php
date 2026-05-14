<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Model\DbTools\ClearModel;
use App\Model\DbTools\MigrationModel;
use App\Model\DbTools\SeedModel;
use App\Security\CsrfTokenManager;
use App\Service\EnvironmentService;
use App\Service\TemplateRenderer;

final class DbToolsController
{
    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly EnvironmentService $environmentService,
        private readonly TemplateRenderer $templateRenderer,
        private readonly MigrationModel $migrationModel,
        private readonly SeedModel $seedModel,
        private readonly ClearModel $clearModel,
    ) {
    }

    public function index(): Response
    {
        if (!$this->environmentService->isDev()) {
            return new Response('Not Found', 404);
        }

        $csrfToken = $this->csrfTokenManager->getOrCreateToken();

        return new Response(
            $this->templateRenderer->render('db-tools/index.tpl', [
                'csrfToken' => $csrfToken,
                'appEnv' => $this->environmentService->current(),
            ])
        );
    }

    public function migrate(): Response
    {
        return $this->runAction(function (): Response {
            $applied = $this->migrationModel->runMigrations();
            $message = $applied === []
                ? 'No new migrations to apply.'
                : 'Applied migrations: ' . implode(', ', $applied);

            return $this->successResponse($message, ['applied' => $applied]);
        });
    }

    public function seed(): Response
    {
        return $this->runAction(function (): Response {
            $stats = $this->seedModel->runSeed(5);

            return $this->successResponse(
                sprintf(
                    'Seed complete. Added categories: %d, posts: %d, relations: %d.',
                    $stats['categories'],
                    $stats['posts'],
                    $stats['relations']
                ),
                $stats
            );
        });
    }

    public function clear(): Response
    {
        return $this->runAction(function (): Response {
            $this->clearModel->clearData();

            return $this->successResponse('Data successfully cleared.', [
                'categories' => 0,
                'posts' => 0,
                'relations' => 0,
            ]);
        });
    }

    private function runAction(callable $action): Response
    {
        $guardResponse = $this->guardDevAndCsrf();
        if ($guardResponse instanceof Response) {
            return $guardResponse;
        }

        try {
            $response = $action();
            if (!$response instanceof Response) {
                return $this->errorResponse('Invalid action response.', 500);
            }

            return $response;
        } catch (\Throwable $e) {
            return $this->errorResponse(
                $this->environmentService->isDev() ? $e->getMessage() : 'Internal Server Error',
                500
            );
        }
    }

    private function guardDevAndCsrf(): ?Response
    {
        if (!$this->environmentService->isDev()) {
            return Response::json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        if (!$this->csrfTokenManager->isValid($_POST['_token'] ?? null)) {
            return Response::json(['status' => 'error', 'message' => 'Invalid CSRF token'], 419);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function successResponse(string $message, array $data = []): Response
    {
        return Response::json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ]);
    }

    private function errorResponse(string $message, int $statusCode): Response
    {
        return Response::json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
