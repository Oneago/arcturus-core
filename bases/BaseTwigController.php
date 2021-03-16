<?php


namespace Oneago\AdaConsole\Bases;

use DateTime;
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
        ]);

        if ($_ENV['DEBUG_MODE'])
            $this->templateEngine->addExtension(new DebugExtension());

        $this->templateEngine->addGlobal('SERVER', $_SERVER);
        $this->templateEngine->addGlobal('GET', $_GET);
        $this->templateEngine->addGlobal('POST', $_POST);
        $this->templateEngine->addGlobal('COOKIE', $_COOKIE);
        $this->templateEngine->addGlobal('ENV', $_ENV);
        if (isset($_SESSION))
            $this->templateEngine->addGlobal('SESSION', $_SESSION);

        $this->templateEngine->addFunction(new TwigFunction('getCss', function (string $cssFile) {
            return sprintf('/css/%s', ltrim($cssFile, '/'));
        }));
        $this->templateEngine->addFunction(new TwigFunction('getJs', function (string $cssFile) {
            return sprintf('/js/%s', ltrim($cssFile, '/'));
        }));
        $this->templateEngine->addFunction(new TwigFunction('getAssets', function (string $assetsFile) {
            return sprintf('/assets/%s', ltrim($assetsFile, '/'));
        }));
        $this->templateEngine->addFunction(new TwigFunction('serialize', function (object $object) {
            return serialize($object);
        }));
        $this->templateEngine->addFunction(new TwigFunction('unserialize', function (string $object) {
            return unserialize($object);
        }));
        $this->templateEngine->addFunction(new TwigFunction('dateDiff', function (DateTime $start, DateTime $finish) {
            $diff = $start->diff($finish);
            return $diff->invert ? $diff->days * -1 : $diff->days;
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

    /**
     * Default return http 401 Unauthorized
     * @return bool
     */
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
