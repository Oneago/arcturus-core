<?php

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
    function view(string $view, string $viewFolder = null, array $customVars = null, string $controllerMethod = 'index'): ViewRequest
    {
        $twigFolder = strtolower($viewFolder !== null ? "$viewFolder/" : ''); // all path to lower
        $view = ucfirst($view); // first capitalized for file formats
        return new ViewRequest("$twigFolder$view", $customVars, $controllerMethod);
    }
}

if (!function_exists('api')) {
    function api(string $apiFile, string $apiFolder = null, array $arrayArgs = null, string $apiMethod = 'index'): ApiRequest
    {
        return new ApiRequest($apiFile, $apiFolder, $arrayArgs, $apiMethod);
    }
}