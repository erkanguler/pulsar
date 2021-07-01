<?php

namespace Erkan\App\Kernel;

abstract class AbstractController
{
    private ViewInterface $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $view, array $variables = []): string|false
    {
        return $this->view->render($view, $variables);
    }

    protected function renderJSON($data, int $code = 200): string|false
    {
        return $this->view->renderJSON($data, $code);
    }

    public function setLayout(string $layout = 'main'): void
    {
        $this->view->setLayout($layout);
    }
}
