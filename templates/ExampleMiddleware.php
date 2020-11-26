<?php


namespace App\Middlewares;


use Oneago\AdaConsole\Bases\MiddlewareInterface;

/**
 * Class ExampleMiddleware
 * @package App\Middlewares
 */
class ExampleMiddleware implements MiddlewareInterface
{
    public function check(): bool
    {
        // TODO: Make your verification and return a boolean value
        return true;
    }
}