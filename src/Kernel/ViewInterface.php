<?php

namespace Erkan\App\Kernel;

interface ViewInterface
{
    public function render(string $view, array $variables = []): string|false;

    public function renderJSON($data, int $code = 200): string|false;

    public function setLayout(string $layout = 'main'): void;
}
