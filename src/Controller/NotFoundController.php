<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Service\TemplateRenderer;

final class NotFoundController
{
    private readonly TemplateRenderer $templateRenderer;

    public function __construct()
    {
        $this->templateRenderer = new TemplateRenderer();
    }

    public function show(): Response
    {
        return new Response(
            $this->templateRenderer->render('errors/404.tpl'),
            404
        );
    }
}
