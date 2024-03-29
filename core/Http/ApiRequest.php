<?php

namespace Oneago\Arcturus\Core\Http;

use Exception;

class ApiRequest
{
    protected string $apiFile;
    protected ?string $apiFolder = null;
    protected ?array $arrayArgs = null;
    protected string $apiMethod = 'index';

    /**
     * @param string $apiFile
     * @param string|null $apiFolder
     * @param array|null $arrayArgs
     * @param string $apiMethod
     */
    public function __construct(string $apiFile, ?string $apiFolder, ?array $arrayArgs, string $apiMethod)
    {
        $this->apiFile = $apiFile;
        $this->apiFolder = $apiFolder;
        $this->arrayArgs = $arrayArgs;
        $this->apiMethod = $apiMethod;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        $api = $this->getApi();

        if (!class_exists($api)) {
            header("Content-Type: application/json");
            http_response_code(404);
            return "{\"error\":\"Resource $this->apiFile not found\"}";
        }

        return call_user_func([
            new $api,
            $this->apiMethod
        ], $this->arrayArgs);

    }

    /**
     * @return string
     */
    private function getApi(): string
    {
        $api = ucfirst($this->apiFile);
        $folder = $this->apiFolder === null ? "" : "\\" . ucfirst($this->apiFolder);
        return "App\Http\Apis$folder\\{$api}Api";
    }
}