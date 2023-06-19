<?php

namespace app\core;

use app\database\DatabaseHost;

/**
 * Class Application
 *
 * @author Raimonds Sierins <raimondssierins@gmail.com>
 * @package app\core
 */
class Application
{
    private static Router $router;
    private static Request $request;
    private static string $ROOT_DIR;
    //public Response $response;
    private static Application $app;
    private static DatabaseHost $db;
    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        self::$request = new Request(
            getallheaders(),
            $_GET,
            json_decode(file_get_contents('php://input'), true)
        );
        //$this->response = new Response();
        self::$router = new Router(self::$request);
        self::$db = new DatabaseHost($config['db']);
    }
    public static function getApp()
    {
        return self::$app;
    }
    public static function getRouter()
    {
        return self::$router;
    }
    public static function getRequest()
    {
        return self::$request;
    }
    public static function getRootDir()
    {
        return self::$ROOT_DIR;
    }
    public static function getDbHost()
    {
        return self::$db;
    }
    // public static function getRequestData()
    // {
    //     $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

    //     if ($requestMethod === 'get') {
    //         return $_GET;
    //     } else {
    //         //var_dump(file_get_contents('php://input'), true);
    //         return json_decode(file_get_contents('php://input'), true);
    //     }
    // }
    public function run()
    {
        self::$router->resolve();
    }
}
