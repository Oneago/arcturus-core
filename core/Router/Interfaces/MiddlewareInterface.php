<?php


namespace Oneago\Arcturus\Core\Router\Interfaces;


interface MiddlewareInterface
{
    public function check(): bool;
}