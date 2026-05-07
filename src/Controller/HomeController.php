<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;

final class HomeController
{
    public function index(): Response
    {
        $content = 'Blog app is running';
        $content .= '<br>';
        $content .= date('Y-m-d H:i:s');

        return new Response($content);
    }
}
