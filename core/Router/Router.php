<?php


namespace Oneago\Arcturus\Core\Router;


use Exception;
use JetBrains\PhpStorm\Pure;
use Oneago\Arcturus\Core\Http\ApiRequest;
use Oneago\Arcturus\Core\Http\ViewRequest;
use Oneago\Arcturus\Core\Router\Interfaces\MiddlewareInterface;
use Oneago\Arcturus\Core\Router\Interfaces\RouterInterface;

class Router implements RouterInterface
{
    private Request $request;
    private bool $is404 = true;
    private bool $isResourceFound = false;
    private ?string $custom404Page;
    private ?string $responseHTML = null;

    public const MIDDLEWARE_REDIRECT_ON_FAIL = 0;
    public const MIDDLEWARE_MESSAGE_ON_FAIL = 1;
    private int $middlewareFailMode = -1;
    private string $middlewareFailAction;

    /**
     * Router constructor.
     */
    #[Pure] public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     * @return bool
     */
    private function checkMiddlewares(MiddlewareInterface...$middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            if (!$middleware->check()) {
                http_response_code(401);
                return false;
            }
        }

        return true;
    }


    public static function enableSessions(): void
    {
        session_start();
        session_regenerate_id(true);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['GET'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['POST'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['PUT'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['PATCH'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['DELETE'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     */
    public function options(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['OPTIONS'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     */
    public function any(string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $pattern, $callable, ...$middlewares);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function map(array $methods, string $pattern, callable $callable, MiddlewareInterface...$middlewares): void
    {
        if (!$this->isResourceFound && $this->isRequestResource($pattern)) {
            if (in_array($this->request->getMethod(), $methods, true)) {
                $args = $this->exportVars($pattern);
                if ($this->checkMiddlewares(...$middlewares)) {
                    $return = $callable(request: $this->request, args: $args);
                    if (!$return instanceof ViewRequest && !$return instanceof ApiRequest && !is_string($return)) {
                        throw new Exception("Callable return isn't a ViewRequest, ApiRequest instance or string");
                    } else if ($return instanceof ViewRequest) {
                        $this->responseHTML = $return->getHTML();
                    } else if ($return instanceof ApiRequest) {
                        $this->responseHTML = $return->run();
                    } else {
                        $this->responseHTML = $return;
                    }
                } else if ($this->middlewareFailMode === self::MIDDLEWARE_REDIRECT_ON_FAIL) {
                    http_response_code(401);
                    header("Location: {$this->middlewareFailAction}");
                } else if ($this->middlewareFailMode === self::MIDDLEWARE_MESSAGE_ON_FAIL) {
                    http_response_code(401);
                    echo $this->middlewareFailAction;
                } else {
                    http_response_code(401);
                    var_dump($this->middlewareFailMode);
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

        $status = count($pathPattern) === 0 ? [false] : [];
        for ($i = 1, $iMax = count($pathPattern); $i <= $iMax; $i++) {
            preg_match('/{\w+}/', $pathPattern[$i], $match);
            if (count($match) !== 0) {
                //$status = true;
                continue;
            }

            $status[] = ($requestObject[$i] ?? null) === $pathPattern[$i];
        }

        return !in_array(false, $status, true);
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
                $$varName = $requestObject[$i] ?? null;
                $args[$varName] = urldecode($requestObject[$i] ?? null);
            }
        }
        return $args;
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
        } elseif ($this->responseHTML !== null) {
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

    /**
     * @param string $middlewareFailAction Redirect url if mode is MIDDLEWARE_REDIRECT_ON_FAIL else if mode is MIDDLEWARE_MESSAGE_ON_FAIL shows message
     * @param int $middlewareFailMode
     */
    public function setCustomMiddlewareFail(string $middlewareFailAction, int $middlewareFailMode = self::MIDDLEWARE_MESSAGE_ON_FAIL): void
    {
        $this->middlewareFailMode = $middlewareFailMode;
        $this->middlewareFailAction = $middlewareFailAction;
    }
}
