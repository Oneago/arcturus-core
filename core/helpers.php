<?php

use JetBrains\PhpStorm\Pure;
use Oneago\Arcturus\Core\Http\ApiRequest;
use Oneago\Arcturus\Core\Http\ViewRequest;
use Oneago\Arcturus\Core\Http\ViewResponse;

if (!function_exists('template')) {
    function template(string $template, $twigVariables = []): ViewResponse
    {
        return new ViewResponse($template, $twigVariables);
    }
}

if (!function_exists('view')) {
    #[Pure] function view(string $view, string $viewFolder = null, array $customVars = null, string $controllerMethod = 'index'): ViewRequest
    {
        return new ViewRequest($view, $viewFolder, $customVars, $controllerMethod);
    }
}

if (!function_exists('api')) {
    #[Pure] function api(string $apiFile, array $arrayArgs = null, string $apiMethod = 'index'): ApiRequest
    {
        return new ApiRequest($apiFile, $arrayArgs, $apiMethod);
    }
}