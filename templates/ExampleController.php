<?php


namespace App\Controllers;


use App\Bases\TwigController;
use Oneago\AdaConsole\Bases\MiddlewareInterface;

/**
 * Class ExampleController is a example class, you can delete or use as a model example for your app
 */
class ExampleController extends TwigController
{
    /**
     * ExampleController constructor.
     * @param MiddlewareInterface ...$middlewares
     */
    public function __construct(MiddlewareInterface...$middlewares)
    {
        parent::__construct(...$middlewares);
        $this->render();
    }

    public function render()
    {
        $status = self::checkMiddlewares();
        if ($status) {
            /** @noinspection PhpUnhandledExceptionInspection */
            echo self::renderHTML("example.twig", [
                "body" => "Example page for basic php Oneago project"
            ]);
        } else {
            // header("location: /{$_SESSION['lang']}/login?redirect={$_SERVER['REDIRECT_URL']}");      // Example
            echo "No correct passing middleware";
        }
    }
}
