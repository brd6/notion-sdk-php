<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Resource\Database;
use Brd6\NotionSdkPhp\Resource\Pagination\PageOrDatabaseResults;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function count;
use function file_get_contents;

class SearchEndpointTest extends TestCase
{
    public function testSearch(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString('search', $url);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_search_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var PageOrDatabaseResults $results */
        $results = $client->search();

        $this->assertInstanceOf(PageOrDatabaseResults::class, $results);

        $this->assertEquals('page_or_database', $results->getType());
        $this->assertEquals('list', $results->getObject());
        $this->assertGreaterThan(0, count($results->getResults()));

        /** @var Database $resultDatabase */
        $resultDatabase = $results->getResults()[0];

        $this->assertEquals('database', $resultDatabase->getObject());
        $this->assertNotEmpty($resultDatabase->getId());
    }
}
