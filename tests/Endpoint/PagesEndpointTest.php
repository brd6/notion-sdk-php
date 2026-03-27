<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\PagesEndpoint;
use Brd6\NotionSdkPhp\Resource\Block\CalloutBlock;
use Brd6\NotionSdkPhp\Resource\Block\FileBlock;
use Brd6\NotionSdkPhp\Resource\Block\Heading1Block;
use Brd6\NotionSdkPhp\Resource\Block\ImageBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\File\External;
use Brd6\NotionSdkPhp\Resource\File\File;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DataSourceIdParent;
use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\AbstractPropertyItem;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\TitlePropertyItem;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\DatePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\FilesPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PropertyItemResults;
use Brd6\NotionSdkPhp\Resource\Property\CalloutProperty;
use Brd6\NotionSdkPhp\Resource\Property\DateProperty;
use Brd6\NotionSdkPhp\Resource\Property\ExternalProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function array_keys;
use function count;
use function file_get_contents;
use function json_decode;
use function sort;

class PagesEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $pages = new PagesEndpoint($client);

        $this->assertInstanceOf(PagesEndpoint::class, $client->pages());
        $this->assertInstanceOf(PagesEndpoint::class, $pages);
    }

    public function testRetrieve(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = $client->pages()->retrieve('4a808e6e-8845-4d49-a447-fb2a4c460f6f');

        $this->assertEquals('page', $page::getResourceType());
        $this->assertNotEmpty($page->getId());
    }

    public function testRetrieveIcon(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_icon_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = $client->pages()->retrieve('b12b6a5c-0ce5-4131-8d37-134a93a3e870');

        $pageIcon = $page->getIcon();

        $this->assertNotEmpty($pageIcon);
        $this->assertInstanceOf(File::class, $pageIcon);
        $this->assertNotEmpty($pageIcon->getFile()->getUrl());
    }

    public function testRetrieveProperties(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_properties_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = $client->pages()->retrieve('1101fb68-d6f1-48c9-bdd4-25004315fda1');

        $this->assertNotEmpty($page->getProperties());
    }

    public function testCreatePage(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('POST', $method);
            $this->assertStringContainsString('pages', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertArrayHasKey('icon', $body);
            $this->assertArrayHasKey('children', $body);
            $this->assertArrayHasKey('properties', $body);
            $this->assertNotEmpty($body['properties']['title']);
            $this->assertStringContainsString(
                'works',
                $body['properties']['title']['title'][0]['text']['content'],
            );

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = new Page();
        $page->setParent((new PageIdParent())->setPageId('4a808e6e88454d49a447fb2a4c460f6f'));
        $page->setIcon((new Emoji())->setEmoji('🎉'));

        $titleProperty = (new TitlePropertyValue())->setTitle([Text::fromContent("It's works!")]);
        $pageProperties = [
            'title' => $titleProperty,
        ];
        $page->setProperties($pageProperties);

        $heading1 = new Heading1Block();
        $heading1Property = new HeadingProperty();
        $heading1Property->setRichText([Text::fromContent('This is a big title')]);

        $heading1->setHeading1($heading1Property);

        $callout = new CalloutBlock();
        $calloutProperty = new CalloutProperty();
        $calloutProperty->setIcon((new Emoji())->setEmoji('😎'));
        $calloutProperty->setRichText([
            Text::fromContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
        ]);

        $callout->setCallout($calloutProperty);

        $externalFileBlock = new FileBlock();
        $file = (new External())
            ->setExternal((new ExternalProperty())
                ->setUrl('https://images.unsplash.com/photo-1648138754688-377bbdf661d9'));
        $externalFileBlock->setFile($file);

        $imageBlock = new ImageBlock();
        $imageFile = (new External())
            ->setExternal((new ExternalProperty())
                ->setUrl('https://images.unsplash.com/photo-1648138754688-377bbdf661d9'));
        $imageBlock->setImage($imageFile);

        $paragraphBlock = new ParagraphBlock();
        $paragraphProperty = new ParagraphProperty();
        $paragraphProperty->setRichText([Text::fromContent('Ut tristique, nisi nec vulputate pellentesque, ' .
                'ipsum lacus aliquet diam, placerat porta est risus sit amet mi. Nunc dictum posuere nibh. ' .
                'Maecenas vitae mollis leo. Praesent vitae eros at ligula convallis luctus eu nec enim'),
        ]);

        $paragraphBlock->setParagraph($paragraphProperty);

        $children = [
            $heading1,
            $imageBlock,
            $externalFileBlock,
            $paragraphBlock,
            $callout,
        ];
        $pageCreated = $client->pages()->create($page, $children);

        $this->assertNotEmpty($pageCreated->getProperties());
    }

    public function testUpdatePage(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('PATCH', $method);
            $this->assertStringContainsString('pages/1e7e638f78864ec591ea54ec7016e146', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertArrayHasKey('icon', $body);
            $this->assertArrayHasKey('properties', $body);
            $this->assertNotEmpty($body['properties']['title']);
            $this->assertStringContainsString(
                'New title',
                $body['properties']['title']['title'][0]['text']['content'],
            );

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = new Page();
        $page->setId('1e7e638f78864ec591ea54ec7016e146');
        $page->setIcon((new Emoji())->setEmoji('🖖'));

        $titleProperty = (new TitlePropertyValue())->setTitle([Text::fromContent('New title!')]);
        $pageProperties = [
            'title' => $titleProperty,
        ];
        $page->setProperties($pageProperties);

        $pageUpdated = $client->pages()->update($page);

        $this->assertNotEmpty($pageUpdated->getProperties());
    }

    public function testUpdatePageFromRetrievedInstancePreservesExistingUpdatePayloadShape(): void
    {
        $requestCount = 0;

        $httpClient = new MockHttpClient(function ($method, $url, $options) use (&$requestCount) {
            $requestCount++;

            if ($requestCount === 1) {
                $this->assertSame('GET', $method);
                $this->assertStringContainsString('pages/4a808e6e-8845-4d49-a447-fb2a4c460f6f', $url);

                return new MockResponseFactory(
                    (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                    [
                        'http_code' => 200,
                    ],
                );
            }

            if ($requestCount === 2) {
                $this->assertSame('PATCH', $method);
                $this->assertStringContainsString('pages/4a808e6e-8845-4d49-a447-fb2a4c460f6f', $url);

                /** @var array $body */
                $body = json_decode($options['body'], true);

                $expectedKeys = [
                    'object',
                    'id',
                    'created_time',
                    'created_by',
                    'last_edited_time',
                    'last_edited_by',
                    'archived',
                    'icon',
                    'cover',
                    'properties',
                    'parent',
                    'url',
                ];

                $actualKeys = array_keys($body);
                sort($expectedKeys);
                sort($actualKeys);
                $this->assertSame($expectedKeys, $actualKeys);
                $this->assertFalse($body['archived']);
                $this->assertStringContainsString(
                    'Updated from retrieved instance',
                    $body['properties']['title']['title'][0]['text']['content'],
                );

                return new MockResponseFactory(
                    (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                    [
                        'http_code' => 200,
                    ],
                );
            }

            $this->fail("Unexpected request count: {$requestCount}");
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $page = $client->pages()->retrieve('4a808e6e-8845-4d49-a447-fb2a4c460f6f');
        $page->setProperties([
            'title' => (new TitlePropertyValue())->setTitle([Text::fromContent('Updated from retrieved instance')]),
        ]);

        $pageUpdated = $client->pages()->update($page);

        $this->assertSame(2, $requestCount);
        $this->assertNotEmpty($pageUpdated->getId());
    }

    public function testCreatePageDoesNotSendArchivedWhenUnset(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('POST', $method);
            $this->assertStringContainsString('pages', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayNotHasKey('archived', $body);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_create_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $page = new Page();
        $page->setParent((new PageIdParent())->setPageId('4a808e6e88454d49a447fb2a4c460f6f'));
        $page->setProperties([
            'title' => (new TitlePropertyValue())->setTitle([Text::fromContent("It's works!")]),
        ]);

        $pageCreated = $client->pages()->create($page);
        $this->assertNotEmpty($pageCreated->getId());
    }

    public function testCreatePageSendsArchivedWhenExplicitlySet(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('POST', $method);
            $this->assertStringContainsString('pages', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayHasKey('archived', $body);
            $this->assertTrue($body['archived']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_create_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $page = new Page();
        $page->setParent((new PageIdParent())->setPageId('4a808e6e88454d49a447fb2a4c460f6f'));
        $page->setProperties([
            'title' => (new TitlePropertyValue())->setTitle([Text::fromContent("It's works!")]),
        ]);
        $page->setArchived(true);

        $pageCreated = $client->pages()->create($page);
        $this->assertNotEmpty($pageCreated->getId());
    }

    public function testUpdatePageSendsArchivedFalseWhenUnset(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('PATCH', $method);
            $this->assertStringContainsString('pages/1e7e638f78864ec591ea54ec7016e146', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayHasKey('archived', $body);
            $this->assertFalse($body['archived']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $page = new Page();
        $page->setId('1e7e638f78864ec591ea54ec7016e146');
        $page->setProperties([
            'title' => (new TitlePropertyValue())->setTitle([Text::fromContent('New title!')]),
        ]);

        $pageUpdated = $client->pages()->update($page);
        $this->assertNotEmpty($pageUpdated->getProperties());
    }

    public function testUpdatePageSendsArchivedWhenExplicitlySetTrue(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('PATCH', $method);
            $this->assertStringContainsString('pages/1e7e638f78864ec591ea54ec7016e146', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertArrayHasKey('archived', $body);
            $this->assertTrue($body['archived']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $page = new Page();
        $page->setId('1e7e638f78864ec591ea54ec7016e146');
        $page->setProperties([
            'title' => (new TitlePropertyValue())->setTitle([Text::fromContent('New title!')]),
        ]);
        $page->setArchived(true);

        $pageUpdated = $client->pages()->update($page);
        $this->assertNotEmpty($pageUpdated->getProperties());
    }

    public function testCreatePageWithDataSourceParent(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('POST', $method);
            $this->assertStringContainsString('pages', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertSame('data_source_id', $body['parent']['type']);
            $this->assertSame('164b19c5-58e5-4a47-a3a9-c905d9519c65', $body['parent']['data_source_id']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $page = new Page();
        $page->setParent((new DataSourceIdParent())->setDataSourceId('164b19c5-58e5-4a47-a3a9-c905d9519c65'));
        $page->setProperties([
            'title' => (new TitlePropertyValue())->setTitle([Text::fromContent("It's works!")]),
        ]);

        $pageCreated = $client->pages()->create($page);
        $this->assertNotEmpty($pageCreated->getId());
    }

    public function testRetrieveProperty(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('GET', $method);
            $this->assertStringContainsString(
                'pages/fed90baa77e9404d80ba3e2736fc8ac4/properties/%3AIiY',
                $url,
            );
            $this->assertStringContainsString('page_size', $url);
            $this->assertArrayHasKey('page_size', $options['query']);
            $this->assertNotEmpty($options['query']['page_size']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_property_item_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var DatePropertyValue $propertyItem */
        $propertyItem = $client->pages()
            ->properties()
            ->retrieve('fed90baa77e9404d80ba3e2736fc8ac4', '%3AIiY');

        $this->assertInstanceOf(AbstractPropertyValue::class, $propertyItem);
        $this->assertEquals('property_item', $propertyItem->getObject());
        $this->assertEquals('date', $propertyItem->getType());
        $this->assertNotEmpty($propertyItem->getId());

        $date = $propertyItem->getDate();
        $this->assertInstanceOf(DateProperty::class, $date);

        $this->assertNotEmpty($date->getStart());
    }

    public function testRetrievePropertyPaginated(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('GET', $method);
            $this->assertStringContainsString(
                'pages/fed90baa77e9404d80ba3e2736fc8ac4/properties/title',
                $url,
            );
            $this->assertStringContainsString('page_size', $url);
            $this->assertArrayHasKey('page_size', $options['query']);
            $this->assertNotEmpty($options['query']['page_size']);

            return new MockResponseFactory(
                (string) file_get_contents(
                    'tests/Fixtures/client_pages_retrieve_page_property_item_paginated_200.json',
                ),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var PropertyItemResults $propertyItem */
        $propertyItem = $client->pages()
            ->properties()
            ->retrieve('fed90baa77e9404d80ba3e2736fc8ac4', 'title');

        $this->assertInstanceOf(AbstractPaginationResults::class, $propertyItem);
        $this->assertEquals('list', $propertyItem->getObject());
        $this->assertEquals('property_item', $propertyItem->getType());
        $this->assertNotEmpty($propertyItem->getResults());

        $resultItem = $propertyItem->getResults()[0];
        $this->assertInstanceOf(AbstractPropertyItem::class, $resultItem);
        $this->assertInstanceOf(TitlePropertyItem::class, $resultItem);

        /** @var Text $text */
        $text = $resultItem->getTitle();
        $textProperty = $text->getText();

        $this->assertNotNull($textProperty);

        $this->assertEquals('Test', $textProperty->getContent());
    }

    public function testRetrieveFilesProperty(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('GET', $method);
            $this->assertStringContainsString(
                'pages/17325df0-7327-4d38-a612-a08ffea61303/properties/%3BEVu',
                $url,
            );

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_property_files_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var FilesPropertyValue $propertyItem */
        $propertyItem = $client->pages()
            ->properties()
            ->retrieve('17325df0-7327-4d38-a612-a08ffea61303', '%3BEVu');

        $this->assertInstanceOf(FilesPropertyValue::class, $propertyItem);
        $this->assertEquals('property_item', $propertyItem->getObject());
        $this->assertEquals('files', $propertyItem->getType());
        $this->assertNotEmpty($propertyItem->getId());

        $files = $propertyItem->getFiles();
        $this->assertGreaterThan(0, count($files));

        $file = $files[0];
        $this->assertInstanceOf(File::class, $file);

        $this->assertNotEmpty($file->getFile()->getUrl());
    }

    public function testCreatePageWithEmptyBotObject(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_create_page_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = new Page();
        $parent = new PageIdParent();
        $parent->setPageId('4a808e6e-8845-4d49-a447-fb2a4c460f6f');
        $page->setParent($parent);

        $titleProperty = new TitlePropertyValue();
        $titleProperty->setTitle([Text::fromContent('Test Page')]);
        $page->setProperties(['title' => $titleProperty]);

        $createdPage = $client->pages()->create($page);

        $this->assertInstanceOf(Page::class, $createdPage);
        $this->assertSame('28689ea8-2c3c-81da-9bbc-fdfd8cfbddfb', $createdPage->getId());
        $this->assertNotNull($createdPage->getCreatedBy());
        $this->assertNotNull($createdPage->getLastEditedBy());
    }
}
