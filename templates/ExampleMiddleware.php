<?php


namespace App\Http\Middlewares;


use Oneago\Arcturus\Core\Router\Interfaces\MiddlewareInterface;

/**
 * Class ExampleMiddleware
 * @package App\Http\Middlewares
 */
class ExampleMiddleware implements MiddlewareInterface
{
    public function check(): bool
    {
        // TODO: Make your verification and return a boolean value
        return true;
    }
}