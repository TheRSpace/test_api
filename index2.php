<?php

use app\core\Application;
use Dotenv\Dotenv;

// error_reporting(E_ALL);
// ini_set('display_errors', '1');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-type: application/json; charset=UTF-8");

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__); //(dirname(__DIR__));
$dotenv->load();

$config = [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'database' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

$app = new Application(dirname(__DIR__), $config);

// $app->router->get('/', function () {
//     return 'Hello World';
// });
$router = Application::getRouter();
$router->get('/test_api/', 'products');
$router->get('/test_api/add/products', 'products');
// $app->router->get('/', function () {
//     return 'Hello World';
// });
echo "okay";
//run application
$app->run();
