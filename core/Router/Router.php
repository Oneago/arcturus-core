<?php


namespace Oneago\Arcturus\Core\Router;


use Exception;
use JetBrains\PhpStorm\Pure;
use Oneago\Arcturus\Core\Http\ViewRequest;
use Oneago\Arcturus\Core\Router\Interfaces\MiddlewareInterface;
use Oneago\Arcturus\Core\Router\Interfaces\RouterInterface;

class Router implements RouterInterface
{
    private Request $request;
    private bool $is404 = true;
    private bool $isResourceFound = false;
    private ?string $custom404Page;
    private ?string $responseHTML;

    /**
     * Router constructor.
     */
    #[Pure] public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     * @param callable $failAction
     * @return Router|null
     */
    public static function middleware(array $middlewares, callable $failAction): ?Router
    {
        $status = true;
        foreach ($middlewares as $middleware) {
            if ($middleware->check()) {
                $failAction();
                $status = false;
            }
        }

        if ($status) {
            return new Router();
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $pattern, callable $callable): void
    {
        $this->map(['GET'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function map(array $methods, string $pattern, callable $callable): void
    {
        if (!$this->isResourceFound && $this->isRequestResource($pattern)) {
            if (in_array($this->request->getMethod(), $methods, true)) {
                $args = $this->exportVars($pattern);
                $return = $callable(request: $this->request, args: $args);
                if (!$return instanceof ViewRequest && !is_string($return)) {
                    throw new Exception("Callable return isn't a ViewRequest instance or string");
                } else if ($return instanceof ViewRequest) {
                    $this->responseHTML = $return->getHTML();
                } else {
                    $this->responseHTML = $return;
                }
                $this->is404 = false;
                $this->isResourceFound = true;
            } else {
                http_response_code(405);
            }
        }
    }

    /**
     * @param string $pathPattern
     * @return bool
     */
    private function isRequestResource(string $pathPattern): bool
    {
        $pathRequest = $this->request->getPathRequest();
        $pathRequest = str_contains($pathRequest, "?") ? substr($pathRequest, 0, strpos($pathRequest, "?")) : $pathRequest;
        $requestObject = array_filter((explode('/', $pathRequest)), fn($x) => !is_null($x) && $x !== '');
        $pathPattern = array_filter((explode('/', $pathPattern)), fn($x) => !is_null($x) && $x !== '');

        if (count($requestObject) === 0 && 0 === count($pathPattern)) {
            return true;
        }

        for ($i = 1, $iMax = count($pathPattern); $i <= $iMax; $i++) {
            preg_match('/{\w+}/', $pathPattern[$i], $match);
            if (count($match) !== 0) {
                continue;
            }

            if ($requestObject[$i] === $pathPattern[$i]) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $PathPatternRequest
     * @return array
     */
    private function exportVars(string $PathPatternRequest): array
    {
        $args = [];
        $requestObject = explode('/', $this->request->getPathRequest());
        $PathPatternRequest = explode('/', $PathPatternRequest);
        for ($i = 0, $iMax = count($PathPatternRequest); $i < $iMax; $i++) {
            preg_match('/{\w+}/', $PathPatternRequest[$i], $match);
            if (count($match) > 0) {
                $varName = str_replace(['{', '}'], ['', ''], $match)[0];
                $$varName = $requestObject[$i];
                $args[$varName] = urldecode($requestObject[$i]);
            }
        }
        return $args;
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $pattern, callable $callable): void
    {
        $this->map(['POST'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $pattern, callable $callable): void
    {
        $this->map(['PUT'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(string $pattern, callable $callable): void
    {
        $this->map(['PATCH'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $pattern, callable $callable): void
    {
        $this->map(['DELETE'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function options(string $pattern, callable $callable): void
    {
        $this->map(['OPTIONS'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function any(string $pattern, callable $callable): void
    {
        $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function redirect(string $target, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $target");
    }

    public function run(): void
    {
        if ($this->is404) {
            http_response_code(404);
            if ($this->custom404Page !== null) {
                echo $this->custom404Page;
            }
        } else {
            echo $this->responseHTML;
        }
    }

    /**
     * @param callable $view Return vie
     */
    public function setCustom404Page(callable $view): void
    {
        $return = $view(request: $this->request);
        if (!$return instanceof ViewRequest) {
            echo "Callable return isn't a ViewRequest instance";
        }
        $this->custom404Page = $return->getHTML();
    }
}
