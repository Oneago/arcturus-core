<?php


namespace App\Http\Controllers;


use Oneago\Arcturus\Core\Http\ViewResponse;


/**
 * Class ExampleController is a example class, you can delete or use as a model example for your app
 */
class ExampleController
{
    public function index($view): ViewResponse
    {
        return template($view, [
            "body" => "Example page for basic php Oneago project"
        ]);
    }
}
