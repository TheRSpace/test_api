<?php

namespace app\migrations;

use app\core\Application;
use app\database\DatabaseHost;
use app\migrate\ProductMigration;
use Dotenv\Dotenv;

// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: *");
//header("Content-type: application/json; charset=UTF-8");

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'database' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

$db = new DatabaseHost($config['db']);
$migration = new ProductMigration($db->getConnection(), __DIR__);
$migration->applyMigrations();

//$app = new Application(__DIR__, $config);
//$app->db->applyMigrations();
