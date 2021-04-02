<?php


namespace Oneago\Arcturus\Core\Router\Interfaces;


interface RouterInterface
{
    /**
     * Add GET route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function get(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add POST route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function post(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add PUT route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function put(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add PATCH route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function patch(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add DELETE route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function delete(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add OPTIONS route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function options(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add route for any HTTP method
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function any(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add route with multiple methods
     *
     * @param string[] $methods Numeric array of HTTP method names
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     * @param MiddlewareInterface ...$middlewares
     */
    public function map(array $methods, string $pattern, callable $callable, MiddlewareInterface...$middlewares): void;

    /**
     * Add a route that sends an HTTP redirect
     *
     * @param string $target
     * @param int $status
     */
    public function redirect(string $target, int $status = 302): void;
}