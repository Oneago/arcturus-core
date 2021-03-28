<?php


namespace Oneago\Arcturus\Core\Router;


class Request
{
    private string $dns;
    private string $root;
    private string $port;
    private string $method;
    private array $urlRequest;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->dns = $_SERVER['SERVER_NAME'];
        $this->root = $_SERVER['HTTP_HOST'];
        $this->port = $_SERVER['SERVER_PORT'];
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->urlRequest = explode('/', $_SERVER['REQUEST_URI'], 3) ?: ['Error splitting URL'];
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
    public function getUrlRequest(): array
    {
        return $this->urlRequest;
    }

    public function getResource(): string
    {
        return $this->urlRequest[1];
    }
}