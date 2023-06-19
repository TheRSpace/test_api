<?php

namespace app\core;

/**
 * Class Response
 */
class Response
{
    private $body;
    private $statusCode;
    private $header;
    private $headers = [];
    public function __construct($body, $statusCode = 200, $headers = [])
    {
        $this->setBody($body);
        $this->setHeaders($headers);
        $this->setStatusCode($statusCode);
    }

    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo json_encode($this->body);
    }
    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setStatusCode($status)
    {
        $this->statusCode = $status;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }
    public function setHeaders($headers)
    {
        $this->header = $headers;
    }
}
