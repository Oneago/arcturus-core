<?php

namespace Oneago\Arcturus\Core\Http;

use Exception;

class ViewRequest
{
    protected string $view;
    protected ?array $customVars = null;
    protected string $controllerMethod = 'index';

    /**
     * @param string $view
     * @param string|null $viewFolder
     * @param array|null $customVars
     * @param string $controllerMethod
     */
    public function __construct(string $view, ?array $customVars, string $controllerMethod)
    {
        $this->view = $view;
        $this->customVars = $customVars;
        $this->controllerMethod = $controllerMethod;
    }


    /**
     * @return string
     * @throws Exception
     */
    public function getHTML(): string
    {
        $controller = $this->getController();

        $response = call_user_func([
            new $controller,
            $this->controllerMethod
        ], $this->view, $this->customVars);

        if ($response instanceof ViewResponse) {
            return $response->render();
        }

        if (is_string($response)) {
            return $response;
        }

        throw new Exception("Error processing request, response no is instance of App\Http\ViewResponse or String", 1);
    }

    /**
     * @return string
     */
    private function getController(): string
    {
        $controller = ucfirst($this->view);
        $controller = implode("\\", array_map("ucfirst", explode("/", $controller))); // ucfirst on all path
        return "App\Http\Controllers\\{$controller}Controller";
    }
}