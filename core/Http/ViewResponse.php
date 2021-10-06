<?php

namespace Oneago\Arcturus\Core\Http;

use App\Config\TwigConfig;

class ViewResponse extends TwigConfig
{
    protected string $twigView;
    protected array $twigVariables = [];

    /**
     * @param string $twigView
     * @param array $twigVariables
     */
    public function __construct(string $twigView, array $twigVariables)
    {
        parent::__construct();
        $this->twigView = $twigView;
        $this->twigVariables = $twigVariables;
    }


    public function render(): string
    {
        $view = ucfirst($this->twigView);
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->renderHTML("$view.twig", $this->twigVariables);
    }
}