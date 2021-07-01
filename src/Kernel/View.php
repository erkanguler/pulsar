<?php

namespace Erkan\App\Kernel;

use Throwable;
use Erkan\App\Exception\InternalServerErrorException;
use function Erkan\App\logError;

class View implements ViewInterface
{

    public string $layout;

    public function __construct()
    {
        $this->setLayout();
    }

    public function render(string $view, array $variables = []): string|false
    {
        try {
            extract($variables, EXTR_PREFIX_ALL, 'view');
            ob_start();
            require_once dirname(dirname(__FILE__)) . "/views/{$view}View.php";
            $content =  ob_get_clean();

            ob_start();
            require_once dirname(dirname(__FILE__)) . "/views/layout/{$this->layout}Layout.php";
            return ob_get_clean();
        } catch (Throwable $th) {
            $errMsg = "Could not load {$view}View";
            logError(__LINE__, __FILE__, $errMsg);
            throw new InternalServerErrorException($errMsg);
        }
    }

    public function renderJSON($data, int $code = 200): string|false
    {
        header('Content-Type: application/json');
        http_response_code($code);
        return json_encode($data);
    }

    public function setLayout(string $layout = 'main'): void
    {
        $this->layout = $layout;
    }
}
