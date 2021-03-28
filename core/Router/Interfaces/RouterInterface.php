<?php


namespace Oneago\Arcturus\Core\Router\Interfaces;


interface RouterInterface
{
    /**
     * Add GET route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     */
    public function get(string $pattern, callable $callable): void;

    /**
     * Add POST route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routi
     */
    public function post(string $pattern, callable $callable): void;

    /**
     * Add PUT route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     */
    public function put(string $pattern, callable $callable): void;

    /**
     * Add PATCH route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routin
     */
    public function patch(string $pattern, callable $callable): void;

    /**
     * Add DELETE route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     */
    public function delete(string $pattern, callable $callable): void;

    /**
     * Add OPTIONS route
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     */
    public function options(string $pattern, callable $callable): void;

    /**
     * Add route for any HTTP method
     *
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     */
    public function any(string $pattern, callable $callable): void;

    /**
     * Add route with multiple methods
     *
     * @param string[] $methods Numeric array of HTTP method names
     * @param string $pattern The route URI pattern
     * @param callable $callable The route callback routine
     */
    public function map(array $methods, string $pattern, callable $callable): void;

    /**
     * Add a route that sends an HTTP redirect
     *
     * @param string $target
     * @param int $status
     */
    public function redirect(string $target, int $status = 302): void;
}