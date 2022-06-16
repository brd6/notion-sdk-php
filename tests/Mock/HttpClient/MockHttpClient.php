<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Mock\HttpClient;

use Brd6\NotionSdkPhp\Util\UrlHelper;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function is_callable;

class MockHttpClient implements HttpClientInterface
{
    /**
     * @var MockResponseFactory|callable(string $method, string $url, array $options):MockResponseFactory
     */
    private $responseFactory;

    /**
     * @param MockResponseFactory|callable(string, string, array):MockResponseFactory $responseFactory
     */
    public function __construct($responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if (is_callable($this->responseFactory)) {
            $options = $this->buildCallableResponseFactoryOptions($request);
            $response = ($this->responseFactory)($request->getMethod(), (string) $request->getUri(), $options)
                ->create();
        } else {
            $response = $this->responseFactory->create();
        }

        return $response;
    }

    private function buildCallableResponseFactoryOptions(RequestInterface $request): array
    {
        return [
            'body' => (string) $request->getBody(),
            'query' => UrlHelper::parseQuery($request->getUri()->getQuery()),
        ];
    }
}
