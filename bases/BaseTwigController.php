<?php


namespace Oneago\AdaConsole\Bases;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


/**
 * Class BaseTwigController is a basic twig loader
 */
abstract class BaseTwigController
{
    protected Environment $templateEngine;
    protected string $templatesPath = "../views";
    /**
     * @var MiddlewareInterface[]
     */
    protected array $middlewares;

    /**
     * BaseTwigController constructor.
     * @param MiddlewareInterface ...$middlewares
     */
    public function __construct(MiddlewareInterface ...$middlewares)
    {
        $this->middlewares = $middlewares;

        $loader = new FilesystemLoader($this->templatesPath);
        $this->templateEngine = new Environment($loader, [
            "debug" => $_ENV["DEBUG_MODE"],
            "cache" => !$_ENV["DEBUG_MODE"]
        ]);

        if ($_ENV['DEBUG_MODE'])
            $this->templateEngine->addExtension(new DebugExtension());

        $this->templateEngine->addGlobal("getScriptName", $_SERVER["SCRIPT_NAME"]);
        $this->templateEngine->addGlobal("getRequestUri", $_SERVER["REQUEST_URI"]);

        $this->templateEngine->addFunction(new TwigFunction('getCss', function ($cssFile) {
            return sprintf('/css/%s', ltrim($cssFile, '/'));
        }));
        $this->templateEngine->addFunction(new TwigFunction('getJs', function ($cssFile) {
            return sprintf('/js/%s', ltrim($cssFile, '/'));
        }));
        $this->templateEngine->addFunction(new TwigFunction('getAssets', function ($assetsFile) {
            return sprintf('/assets/%s', ltrim($assetsFile, '/'));
        }));
        $this->templateEngine->addFunction(new TwigFunction('getServerVars', function () {
            return $_SERVER;
        }));
        $this->templateEngine->addFunction(new TwigFunction('getGetVars', function () {
            return $_GET;
        }));
        $this->templateEngine->addFunction(new TwigFunction('getPostVars', function () {
            return $_POST;
        }));
        $this->templateEngine->addFunction(new TwigFunction('getCookieVars', function () {
            return $_COOKIE;
        }));
        $this->templateEngine->addFunction(new TwigFunction('getSessionVars', function () {
            return $_SESSION;
        }));
        $this->templateEngine->addFunction(new TwigFunction('getEnvVars', function () {
            return $_ENV;
        }));

        $this->checkMiddlewares();
    }

    /**
     * @param string $fileName Twig file to load
     * @param array $data Twig data to pass
     * @return string Twig rendered HTML
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function renderHTML(string $fileName, array $data = []): string
    {
        return $this->templateEngine->render($fileName, $data);
    }

    protected function checkMiddlewares(): bool
    {
        foreach ($this->middlewares as $middleware) {
            if (!$middleware->check()) {
                http_response_code(401);
                return false;
            }
        }
        return true;
    }

    public abstract function render();
}
