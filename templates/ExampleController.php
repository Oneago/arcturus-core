<?php


namespace App\Controllers;


use App\Bases\TwigController;
use Oneago\AdaConsole\Bases\MiddlewareInterface;

/**
 * Class ExampleController is a example class, you can delete or use as a model example for your app
 */
class ExampleController extends TwigController
{
    private string $body;

    /**
     * ExampleController constructor.
     * @param string $body Page body
     * @param MiddlewareInterface ...$middlewares
     */
    public function __construct(string $body, MiddlewareInterface...$middlewares)
    {
        parent::__construct($middlewares);
        $this->body = $body;
        $this->render();
    }

    public function render()
    {
        $status = self::checkMiddlewares();
        if ($status) {
            /** @noinspection PhpUnhandledExceptionInspection */
            echo self::renderHTML("example.twig", [
                "body" => $this->body
            ]);
        } else {
            echo "No correct passing middleware";
        }
    }
}
