<?php

namespace app\core;

/**
 * Class Request
 */
class Request
{
    private $headers;
    private $params;
    private $body;

    public function __construct($headers, $params, $body)
    {
        $this->headers = $headers;
        $this->params = $params;
        $this->body = $body;
    }
    public function getHeader($name)
    {
        return $this->headers[$name] ?? null;
    }
    public function getParams($name)
    {
        return $this->params[$name] ?? null;
    }
    public function getBody()
    {
        return $this->body;
    }
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    // public function getBody()
    // {
    //     $body = [];
    //     if ($this->getMethod() === 'get') {
    //         echo "here";
    //         foreach ($_GET as $key => $value) {
    //             $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    //         }
    //     }
    //     if ($this->getMethod() === 'post') {
    //         foreach ($_POST as $key => $value) {
    //             $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    //         }
    //     }
    //     return $body;
    // }
}
