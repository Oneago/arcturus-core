<?php

namespace Oneago\Arcturus\Core\Http;

use App\Config\TwigConfig;

/**
 * @method renderHTML(string $string, array $twigVariables)
 */
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
        return $this->renderHTML("$this->twigView.twig", $this->twigVariables);
    }
}