<?php

namespace app;

use app\controllers\ProductController;
use app\core\Application;
use Dotenv\Dotenv;

// error_reporting(E_ALL);
// ini_set('display_errors', '1');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
//header("Access-Control-Max-Age: 3600");
header("Content-type: application/json; charset=UTF-8");

require_once dirname(__DIR__) . '/vendor/autoload.php';
// require_once dirname(__DIR__) . '/core/HandleError.php';
// set_exception_handler("/../core/HandleError::handleException()");
$dotenv = Dotenv::createImmutable(dirname(__DIR__)); //(dirname(__DIR__));
$dotenv->load();

$config = [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'database' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
        'port' => $_ENV['DB_PORT']
    ]
];

$app = new Application(dirname(__DIR__), $config);


$router = Application::getRouter();
$router->get('/', [ProductController::class, 'get']);
$router->options('/product', [ProductController::class, 'handleOptionsRequest']);
$router->get('/products', [ProductController::class, 'getAll']);
$router->get('/validate/', [ProductController::class, 'checkProductSku']);
$router->post('/product', [ProductController::class, 'createProduct']);
$router->get('/product/', [ProductController::class, 'showById']);
$router->get('/product/sku/', [ProductController::class, 'showBySku']);
$router->delete('/product/', [ProductController::class, 'deleteProduct']);
$router->get('/type', [ProductController::class, 'getTypes']);
//run application
$app->run();
