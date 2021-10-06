<?php


namespace Oneago\Arcturus\Core\config;

use DateTime;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


/**
 * Class BaseTwigConfig is a basic twig loader
 */
abstract class BaseTwigConfig
{
    protected Environment $templateEngine;

    /**
     * BaseTwigConfig constructor.
     */
    public function __construct()
    {
        $loader = new FilesystemLoader($this->getViewsDirs());
        $this->templateEngine = new Environment($loader, [
            "debug" => $_ENV["DEBUG_MODE"],
        ]);

        if ($_ENV['DEBUG_MODE']) {
            $this->templateEngine->addExtension(new DebugExtension());
        }

        $this->templateEngine->addGlobal('SERVER', $_SERVER);
        $this->templateEngine->addGlobal('GET', $_GET);
        $this->templateEngine->addGlobal('POST', $_POST);
        $this->templateEngine->addGlobal('COOKIE', $_COOKIE);
        $this->templateEngine->addGlobal('ENV', $_ENV);
        if (isset($_SESSION)) {
            $this->templateEngine->addGlobal('SESSION', $_SESSION);
        }

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
    }

    /**
     * @return array
     */
    private function getViewsDirs(): array
    {
        $parentDir = __DIR__ . "/../../../../../views";
        $dirs = [$parentDir];

        // Scan directories in view folder and returned in array
        $scan = scandir($parentDir);
        foreach ($scan as $x) {
            if (is_dir("$parentDir/$x") && $x !== '.' && $x !== '..') {
                $dirs[] = "$parentDir/$x";
            }
        }
        return $dirs;
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

    abstract public function render();
}
