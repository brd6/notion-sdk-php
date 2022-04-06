<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\DatabasesEndpoint;
use Brd6\NotionSdkPhp\Resource\Pagination\PageResults;
use Brd6\Test\NotionSdkPhp\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function count;
use function file_get_contents;

class DatabasesEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $databases = new DatabasesEndpoint($client);

        $this->assertInstanceOf(DatabasesEndpoint::class, $client->databases());
        $this->assertInstanceOf(DatabasesEndpoint::class, $databases);
    }

    public function testQueryDatabase(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertStringContainsString('databases/a5926cb0-9070-4fea-94f7-494e59a0e75c/query', $url);
                $this->assertStringContainsString('page_size', $url);
                $this->assertArrayHasKey('page_size', $options['query']);
                $this->assertNotEmpty($options['query']['page_size']);
            }

            return new MockResponse(
                (string) file_get_contents('tests/fixtures/client_databases_query_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var PageResults $paginationResponse */
        $paginationResponse = $client->databases()->query('a5926cb0-9070-4fea-94f7-494e59a0e75c');

        $this->assertNotNull($paginationResponse);
        $this->assertInstanceOf(PageResults::class, $paginationResponse);

        $this->assertEquals('page', $paginationResponse->getType());
        $this->assertEquals('list', $paginationResponse->getObject());
        $this->assertGreaterThan(0, count($paginationResponse->getResults()));

        $resultPage = $paginationResponse->getResults()[0];

        $this->assertEquals('page', $resultPage->getObject());
        $this->assertNotEmpty($resultPage->getId());
    }
}
