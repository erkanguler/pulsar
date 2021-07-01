<?php

namespace Erkan\App\Kernel;

class Router
{

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    private array $routes = [];
    private Request $req;

    public function __construct(Request $req)
    {
        $this->req = $req;
    }

    public function getHandler(): array|false
    {
        return $this->routes[$this->req->getMethod()][$this->req->getPath()] ?? false;
    }

    public function get(string $path, array $handler): void
    {
        $this->routes[self::METHOD_GET][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes[self::METHOD_POST][$path] = $handler;
    }

    public function put(string $path, array $handler): void
    {
        $this->routes[self::METHOD_PUT][$path] = $handler;
    }

    public function delete(string $path, array $handler): void
    {
        $this->routes[self::METHOD_DELETE][$path] = $handler;
    }
}
