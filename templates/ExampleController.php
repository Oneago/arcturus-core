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
<<<<<<< HEAD
        $status = self::checkMiddlewares();
        if ($status) {
            /** @noinspection PhpUnhandledExceptionInspection */
            echo self::renderHTML("example.twig", [
                "body" => "Example page for basic php Oneago project. Start creating."
            ]);
        } else {
            // header("location: /{$_SESSION['lang']}/login?redirect={$_SERVER['REDIRECT_URL']}");      // Example
            echo "No correct passing middleware";
        }
=======
        return template($view, [
            "body" => "Example page for basic php Oneago project"
        ]);
>>>>>>> refs/remotes/origin/master
    }
}
