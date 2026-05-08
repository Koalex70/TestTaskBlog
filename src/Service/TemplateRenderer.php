<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\Config;
use RuntimeException;
use Smarty\Smarty;

final class TemplateRenderer
{
    private readonly Smarty $smarty;

    public function __construct()
    {
        $this->smarty = new Smarty();

        $templateConfig = Config::namespace('template');

        $templateDir = $templateConfig['template_dir'] ?? null;
        $compileDir = $templateConfig['compile_dir'] ?? null;
        $cacheDir = $templateConfig['cache_dir'] ?? null;

        if (!is_string($templateDir) || !is_string($compileDir) || !is_string($cacheDir)) {
            throw new RuntimeException('Template config must define string paths: template_dir, compile_dir, cache_dir.');
        }

        $this->smarty->setTemplateDir($templateDir);
        $this->smarty->setCompileDir($compileDir);
        $this->smarty->setCacheDir($cacheDir);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template);
    }
}
