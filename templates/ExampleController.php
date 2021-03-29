<?php


namespace App\Http\Controllers;


use Oneago\Arcturus\Core\Http\ViewResponse;


/**
 * Class ExampleController is a example class, you can delete or use as a model example for your app
 */
class ExampleController
{
    public function index(string $view, ?array $customVars): ViewResponse
    {
        $twigVars = [
            "body" => "Example page for basic php Oneago project. Start creating."
        ];

        if ($customVars !== null) {
            $twigVars = array_merge($customVars, $twigVars);
        }
        return template($view, $twigVars);
    }
}