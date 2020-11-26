<?php


namespace Oneago\AdaConsole\Bases;


interface MiddlewareInterface
{
    public function check(): bool;
}