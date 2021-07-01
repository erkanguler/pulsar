<?php

namespace Erkan\App\Kernel;

class Request
{

    public function __construct()
    {
    }


    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) ?? '';
    }

    public function getPath()
    {
        if (preg_match('%^([^?]*)%i', $_SERVER['REQUEST_URI'], $match)) {
            return $match[1];
        }
        return '';
    }
}
