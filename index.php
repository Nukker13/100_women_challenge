<?php

declare(strict_types=1);

use App\Controllers\GameController;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\RouteCollector;

error_reporting(E_ALL);

define('VIEW_PATH', __DIR__ . '/views');
define('CONTROLLER_PATH', __DIR__ . '/Controllers');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

require __DIR__ . '/vendor/autoload.php';

$router = new RouteCollector();
$router->get('/', [GameController::class, 'index']);
$router->post('/check-name', [GameController::class, 'checkName']);

$dispatcher = new Dispatcher($router->getData());

try {
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $path);
    echo $response;
} catch (\Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {
    http_response_code(404);
    echo '404 Not Found';
} catch (\Phroute\Phroute\Exception\HttpMethodNotAllowedException $e) {
    http_response_code(405);
    echo '405 Method Not Allowed';
}

