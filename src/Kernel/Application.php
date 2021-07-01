<?php

namespace Erkan\App\Kernel;

use Erkan\App\Exception\InternalServerErrorException;
use Erkan\App\Exception\NotFoundException;

class Application
{

    public function __construct()
    {
    }

    public function instantiateHandler(mixed $handler)
    {
        if (!$handler) {
            throw new NotFoundException();
        }

        if (is_array($handler)) {

            if (count($handler) != 2) {
                throw new InternalServerErrorException();
            }

            $controllerName = $handler[0];
            $controllerMethodName = $handler[1];

            if (class_exists($controllerName)) {
                $instance = new $controllerName();
                echo $instance->{$controllerMethodName}();
            } else {
                throw new InternalServerErrorException();
            }
        }
    }
}
