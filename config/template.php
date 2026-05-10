<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);

return [
    'template_dir' => $rootPath . '/templates',
    'compile_dir' => $rootPath . '/var/cache/smarty',
    'cache_dir' => $rootPath . '/var/cache/smarty-cache',
];
