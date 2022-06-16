<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Mock\HttpClient;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class MockResponseFactory
{
    private ?string $body;

    /**
     * @var array{http_code?: int, response_headers?: array}
     */
    private array $info;

    /**
     * @param string|null $body
     * @param array{http_code?: int, response_headers?: array} $info
     */
    public function __construct(?string $body, array $info = [])
    {
        $this->body = $body;
        $this->info = $info;
    }

    /**
     * @return MockInterface|ResponseInterface
     */
    public function create()
    {
        $responseStream = Mockery::mock(StreamInterface::class, [
            'getContents' => $this->body,
        ]);

        return Mockery::mock(ResponseInterface::class, [
            'getBody' => $responseStream,
            'getStatusCode' => $this->info['http_code'],
            'getHeaders' => $this->info['response_headers'] ?? [],
        ]);
    }
}
