<?php

ini_set('display_errors', '0');

use Erkan\App\Controller\IndexController;
use Erkan\App\Controller\DashboardController;
use Erkan\App\Controller\DataGenerationController;
use Erkan\App\Kernel\Request;
use Erkan\App\Kernel\Router;
use Erkan\App\Kernel\Application;
use Erkan\App\Exception\InternalServerErrorException;
use Erkan\App\Exception\NotFoundException;

require_once dirname(__FILE__) . '/vendor/autoload.php';

$req = new Request();
$router = new Router($req);

// Set up routes
$router->get('/', [IndexController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'dashboard']);
$router->post('/dashboard', [DashboardController::class, 'dashboard']);
$router->get('/datageneration', [DataGenerationController::class, 'index']);
$router->post('/datageneration', [DataGenerationController::class, 'generatedata']);

$app = new Application();

try {
    $app->instantiateHandler($router->getHandler());
} catch (NotFoundException $exc) {
    http_response_code(404);
    echo $exc->getMessage();
} catch (InternalServerErrorException $exc) {
    http_response_code(500);
    echo $exc->getMessage();
} catch (Exception $exc) {
    http_response_code(500);
    echo 'Internal Server error';
}
