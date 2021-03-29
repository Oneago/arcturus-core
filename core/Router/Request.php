<?php


namespace Oneago\Arcturus\Core\Router;


class Request
{
    private string $dns;
    private string $root;
    private string $port;
    private string $method;
    private string $pathRequest;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->dns = $_SERVER['SERVER_NAME'];
        $this->root = $_SERVER['HTTP_HOST'];
        $this->port = $_SERVER['SERVER_PORT'];
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->pathRequest = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getDns(): string
    {
        return $this->dns;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getPathRequest(): string
    {
        return $this->pathRequest;
    }
}