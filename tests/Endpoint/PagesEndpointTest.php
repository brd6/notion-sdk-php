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
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\AbstractPropertyItem;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\TitlePropertyItem;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\DatePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PropertyItemResults;
use Brd6\NotionSdkPhp\Resource\Property\CalloutProperty;
use Brd6\NotionSdkPhp\Resource\Property\DateProperty;
use Brd6\NotionSdkPhp\Resource\Property\ExternalProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\Test\NotionSdkPhp\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function file_get_contents;
use function json_decode;

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
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_pages_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $page = $client->pages()->retrieve('4a808e6e-8845-4d49-a447-fb2a4c460f6f');

        $this->assertEquals('page', $page::getResourceType());
        $this->assertNotEmpty($page->getId());
    }

    public function testRetrieveProperties(): void
    {
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_pages_retrieve_page_properties_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

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

            return new MockResponse(
                (string) file_get_contents('tests/fixtures/client_pages_retrieve_page_200.json'),
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

            return new MockResponse(
                (string) file_get_contents('tests/fixtures/client_pages_retrieve_page_200.json'),
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

            return new MockResponse(
                (string) file_get_contents('tests/fixtures/client_pages_retrieve_page_property_item_200.json'),
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

            return new MockResponse(
                (string) file_get_contents(
                    'tests/fixtures/client_pages_retrieve_page_property_item_paginated_200.json',
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
}
