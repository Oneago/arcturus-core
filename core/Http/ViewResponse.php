<?php

namespace Oneago\Arcturus\Core\Http;

use App\Config\TwigConfig;

class ViewResponse extends TwigConfig
{
    /**
     * ViewResponse constructor.
     * @param string $twigView
     * @param array $twigVariables
     */
    public function __construct(protected string $twigView, protected array $twigVariables = [])
    {
        parent::__construct();
    }

    public function render(): string
    {
        $view = ucfirst($this->twigView);
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->renderHTML("$view.twig", $this->twigVariables);
    }
}