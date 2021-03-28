<?php

use Oneago\Arcturus\Core\Http\ViewRequest;
use Oneago\Arcturus\Core\Http\ViewResponse;

if (!function_exists('template')) {
    function template(string $template, $twigVariables = []): ViewResponse
    {
        return new ViewResponse($template, $twigVariables);
    }
}

if (!function_exists('view')) {
    function view(string $view, string $viewFolder = null): ViewRequest
    {
        return new ViewRequest($view, $viewFolder);
    }
}