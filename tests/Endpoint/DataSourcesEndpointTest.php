<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\DataSourcesEndpoint;
use Brd6\NotionSdkPhp\Resource\DataSource;
use Brd6\NotionSdkPhp\Resource\Database\DatabaseRequest;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\TitlePropertyObject;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DataSourceIdParent;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Pagination\PageOrDataSourceResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function count;
use function file_get_contents;
use function json_decode;

class DataSourcesEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $dataSources = new DataSourcesEndpoint($client);

        $this->assertInstanceOf(DataSourcesEndpoint::class, $client->dataSources());
        $this->assertInstanceOf(DataSourcesEndpoint::class, $dataSources);
    }

    public function testRetrieveDataSource(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('GET', $method);
            $this->assertStringContainsString('data_sources/164b19c5-58e5-4a47-a3a9-c905d9519c65', $url);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $dataSource = $client->dataSources()->retrieve('164b19c5-58e5-4a47-a3a9-c905d9519c65');

        $this->assertSame('data_source', $dataSource->getObject());
        $this->assertNotEmpty($dataSource->getId());
    }

    public function testQueryDataSource(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString('data_sources/164b19c5-58e5-4a47-a3a9-c905d9519c65/query', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayHasKey('page_size', $body);
            $this->assertArrayHasKey('filter', $body);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_query_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $databaseRequest = (new DatabaseRequest())->setFilter([
            'property' => 'Name',
            'title' => [
                'is_not_empty' => true,
            ],
        ]);
        $paginationRequest = (new PaginationRequest())->setPageSize(1);

        /** @var PageOrDataSourceResults $results */
        $results = $client->dataSources()->query(
            '164b19c5-58e5-4a47-a3a9-c905d9519c65',
            $databaseRequest,
            $paginationRequest,
        );

        $this->assertInstanceOf(PageOrDataSourceResults::class, $results);
        $this->assertGreaterThan(0, count($results->getResults()));
        $this->assertInstanceOf(Page::class, $results->getResults()[0]);
        $this->assertInstanceOf(DataSourceIdParent::class, $results->getResults()[0]->getParent());
    }

    public function testUpdateDataSource(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('PATCH', $method);
            $this->assertStringContainsString('data_sources/164b19c5-58e5-4a47-a3a9-c905d9519c65', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayHasKey('title', $body);
            $this->assertArrayHasKey('properties', $body);
            $this->assertArrayHasKey('in_trash', $body);
            $this->assertStringContainsString('"title":{}', (string) $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_update_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $dataSource = (new DataSource())
            ->setId('164b19c5-58e5-4a47-a3a9-c905d9519c65')
            ->setTitle([Text::fromContent('My Task Tracker Updated')])
            ->setProperties(['Name' => new TitlePropertyObject()])
            ->setInTrash(false);

        $updated = $client->dataSources()->update($dataSource);

        $this->assertSame('data_source', $updated->getObject());
    }

    public function testCreateDataSource(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString('data_sources', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayHasKey('parent', $body);
            $this->assertArrayHasKey('title', $body);
            $this->assertArrayHasKey('properties', $body);
            $this->assertSame('database_id', $body['parent']['type']);
            $this->assertStringContainsString('"title":{}', (string) $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_create_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $dataSource = (new DataSource())
            ->setParent((new DatabaseIdParent())->setDatabaseId('248104cd-477e-80fd-b757-e945d38000bd'))
            ->setTitle([Text::fromContent('Archive')])
            ->setProperties(['Name' => new TitlePropertyObject()]);

        $created = $client->dataSources()->create($dataSource);

        $this->assertSame('data_source', $created->getObject());
        $this->assertNotEmpty($created->getId());
    }
}
