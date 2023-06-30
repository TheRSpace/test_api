<?php

namespace app\core;

use app\core\Request;

/**
 * Class Router
 *
 * @author Raimonds Sierins <raimondssierins@gmail.com>
 * @package app\core
 */
class Router
{
    //protected Router $router;
    private Request $request;
    //public Response $response;
    private array $routes = [];
    public function __construct(Request $request)
    {
        $this->request = $request;
        //$this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }
    public function put($path, $callback)
    {
        $this->routes['put'][$path] = $callback;
    }
    public function delete($path, $callback)
    {
        $this->routes['delete'][$path] = $callback;
    }
    public function options($path, $callback)
    {
        $this->routes['options'][$path] = $callback;
    }
    // public function resolve()
    // {
    //     $path = $this->request->getPath();
    //     $method = $this->request->getMethod();
    //     $callback = $this->routes[$method][$path] ?? false;
    //     if ($callback === false) {
    //         $this->response->setStatusCode(404);
    //         echo "Not Found";
    //         exit;
    //     }
    //     // if (is_string($callback)) {
    //     //     return $this->renderView($callback);
    //     // }
    //     if (is_array($callback)) {
    //         $callback[0] = new $callback[0]();
    //     }
    //     // $className = $callback[0];
    //     // $function = $callback[1];
    //     // $object = new $className();
    //     // $object->$function();
    //     call_user_func($callback);
    // }
    public function resolve()
    {
        //$method = $this->request->getHeader('REQUEST_METHOD');
        //$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? null;
        if ($method === 'options') {
            $this->handleOptionsRequest();
            return;
        }
        if (!$callback) {
            $response = new Response(['error' => 'Not Found'], 400);
            $response->send();
            return;
        }
        if (is_array($callback)) {
            $callback[0] = new $callback[0]();
        }


        //$response = call_user_func($callback);
        $response = call_user_func($callback, [$this->request]);
        $response->send();
    }
    protected function handleOptionsRequest()
    {
        $response = new Response(['message' => "OK"], 200);
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', '*');
        $response->setHeader('Access-Control-Max-Age', '86400');
        $response->send();
    }
    protected function handleOptionsDeleteRequest()
    {
        $response = new Response(['message' => "OK"], 200);
        $response->setHeader('Access-Control-Allow-Methods', 'GET, DELETE');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $response->setHeader('Access-Control-Max-Age', '86400');
        $response->send();
    }
}
