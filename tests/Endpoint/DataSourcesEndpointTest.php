<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\DataSourcesEndpoint;
use Brd6\NotionSdkPhp\Resource\DataSource;
use Brd6\NotionSdkPhp\Resource\DataSource\DataSourceTemplate;
use Brd6\NotionSdkPhp\Resource\DataSource\DataSourceTemplateResults;
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

    public function testListTemplates(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('GET', $method);
            $this->assertStringContainsString(
                'data_sources/164b19c5-58e5-4a47-a3a9-c905d9519c65/templates',
                $url,
            );
            $this->assertArrayNotHasKey('name', $options['query']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_list_templates_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $results = $client->dataSources()->listTemplates('164b19c5-58e5-4a47-a3a9-c905d9519c65');

        $this->assertInstanceOf(DataSourceTemplateResults::class, $results);
        $this->assertFalse($results->isHasMore());
        $this->assertNull($results->getNextCursor());
        $this->assertCount(2, $results->getTemplates());

        $template = $results->getTemplates()[0];
        $this->assertInstanceOf(DataSourceTemplate::class, $template);
        $this->assertEquals('195de922-1179-449f-ab80-75a27c979105', $template->getId());
        $this->assertEquals('Bug report', $template->getName());
        $this->assertTrue($template->isDefault());
        $this->assertFalse($results->getTemplates()[1]->isDefault());
    }

    public function testListTemplatesWithNameAndPagination(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('Bug report', $options['query']['name']);
            $this->assertEquals(5, (int) $options['query']['page_size']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_list_templates_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $client->dataSources()->listTemplates(
            '164b19c5-58e5-4a47-a3a9-c905d9519c65',
            'Bug report',
            (new PaginationRequest())->setPageSize(5),
        );
    }

    public function testUpdateNormalizesHydratedEmptyPropertyConfigurations(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertSame([], $body['properties']['Date']['date']);
            $this->assertStringContainsString('"date":{}', $options['body']);
            $this->assertArrayNotHasKey('Last edited by', $body['properties']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
            true,
        );
        $rawData['properties']['Date'] = ['id' => 'a1', 'name' => 'Date', 'type' => 'date', 'date' => []];
        $rawData['properties']['Last edited by'] = ['id' => 'b2', 'name' => 'Last edited by', 'type' => 'last_edited_by', 'last_edited_by' => []];

        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData($rawData);

        $client->dataSources()->update($dataSource);
    }

    public function testUpdateObjectifiesHydratedSinglePropertyRelation(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);

            $relation = $body['properties']['Project']['relation'];
            $this->assertSame('single_property', $relation['type']);
            $this->assertSame([], $relation['single_property']);
            $this->assertSame('target-ds', $relation['data_source_id']);
            $this->assertStringContainsString('"single_property":{}', (string) $options['body']);
            $this->assertStringNotContainsString('"single_property":[]', (string) $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
            true,
        );
        $rawData['properties']['Project'] = [
            'id' => 'p1',
            'name' => 'Project',
            'type' => 'relation',
            'relation' => [
                'data_source_id' => 'target-ds',
                'type' => 'single_property',
                'single_property' => [],
            ],
        ];

        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData($rawData);

        $client->dataSources()->update($dataSource);
    }

    public function testUpdateKeepsPopulatedRelationFlatWithoutNestedObject(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);

            $relation = $body['properties']['Projects']['relation'];
            $this->assertArrayNotHasKey('single_property', $relation);
            $this->assertArrayNotHasKey('dual_property', $relation);
            $this->assertArrayNotHasKey('type', $relation);
            $this->assertSame('Tasks', $relation['synced_property_name']);
            $this->assertStringNotContainsString('"dual_property":', (string) $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData((array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
            true,
        ));

        $client->dataSources()->update($dataSource);
    }

    public function testUpdateObjectifiesHydratedDualPropertyRelation(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);

            $relation = $body['properties']['Project']['relation'];
            $this->assertSame('dual_property', $relation['type']);
            $this->assertSame([], $relation['dual_property']);
            $this->assertStringContainsString('"dual_property":{}', (string) $options['body']);
            $this->assertStringNotContainsString('"dual_property":[]', (string) $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
            true,
        );
        $rawData['properties']['Project'] = [
            'id' => 'p1',
            'name' => 'Project',
            'type' => 'relation',
            'relation' => [
                'data_source_id' => 'target-ds',
                'type' => 'dual_property',
                'dual_property' => [],
            ],
        ];

        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData($rawData);

        $client->dataSources()->update($dataSource);
    }
}
