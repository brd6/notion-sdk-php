<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\BlocksEndpoint;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\ChildPageBlock;
use Brd6\NotionSdkPhp\Resource\Block\Heading3Block;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Block\TableRowBlock;
use Brd6\NotionSdkPhp\Resource\Pagination\BlockResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\NotionSdkPhp\Resource\UserInterface;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function count;
use function file_get_contents;
use function json_decode;

class BlocksEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $blocks = new BlocksEndpoint($client);

        $this->assertInstanceOf(BlocksEndpoint::class, $client->blocks());
        $this->assertInstanceOf(BlocksEndpoint::class, $blocks);
    }

    public function testRetrieve(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $block = $client->blocks()->retrieve('0c940186-ab70-4351-bb34-2d16f0635d49');

        $this->assertEquals('block', $block::getResourceType());
        $this->assertNotEmpty($block->getType());
        $this->assertNotEmpty($block->jsonSerialize());
    }

    public function testRetrieveChildPage(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_child_page_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $block = $client->blocks()->retrieve('4a808e6e-8845-4d49-a447-fb2a4c460f6f');

        $this->assertInstanceOf(ChildPageBlock::class, $block);
        $this->assertEquals('child_page', $block->getType());
        $this->assertNotEmpty($block->getId());
        $this->assertEquals('4a808e6e-8845-4d49-a447-fb2a4c460f6f', $block->getId());

        $this->assertInstanceOf(ChildPageProperty::class, $block->getChildPage());
        $this->assertNotEmpty($block->getChildPage()->getTitle());

        $this->assertInstanceOf(UserInterface::class, $block->getCreatedBy());
        $this->assertEquals('user', $block->getCreatedBy()->getObject());
        $this->assertNotEmpty($block->getCreatedBy()->getId());

        $this->assertNotEmpty($block->getCreatedTime());
        $this->assertNotEmpty($block->getLastEditedTime());
        $this->assertNotEmpty($block->getLastEditedTime());
    }

    public function testUpdateBlock(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            if ($method === 'PATCH') {
                $this->assertStringContainsString('blocks/0c940186-ab70-4351-bb34-2d16f0635d49', $url);

                /** @var array $body */
                $body = json_decode($options['body'], true);

                $this->assertArrayHasKey('paragraph', $body);
                $this->assertArrayHasKey('archived', $body);
                $this->assertNotEmpty($body['paragraph']);
                $this->assertStringContainsString(
                    'Hello world!',
                    $body['paragraph']['rich_text'][0]['text']['content'],
                );
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var ParagraphBlock $block */
        $block = $client->blocks()->retrieve('0c940186-ab70-4351-bb34-2d16f0635d49');

        $this->assertNotNull($block->getParagraph());

        $richText = $block->getParagraph()->getRichText()[0];

        $this->assertInstanceOf(Text::class, $richText);
        $this->assertNotNull($richText->getText());

        $richText->getText()->setContent('Hello world!');

        $client->blocks()->update($block);
    }

    public function testRetrieveBlockChildren(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertStringContainsString('blocks/03cd5dca-84f7-456f-b7e6-aad92d5f69fd/children', $url);
                $this->assertStringContainsString('page_size', $url);
                $this->assertArrayHasKey('page_size', $options['query']);
                $this->assertNotEmpty($options['query']['page_size']);
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_children_default_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var BlockResults $paginationResponse */
        $paginationResponse = $client->blocks()->children()->list('03cd5dca-84f7-456f-b7e6-aad92d5f69fd');

        $this->assertNotNull($paginationResponse);
        $this->assertInstanceOf(BlockResults::class, $paginationResponse);

        $this->assertEquals('block', $paginationResponse->getType());
        $this->assertEquals('list', $paginationResponse->getObject());
        $this->assertGreaterThan(0, count($paginationResponse->getResults()));

        $resultBlock = $paginationResponse->getResults()[0];

        $this->assertEquals('block', $resultBlock->getObject());
        $this->assertNotEmpty($resultBlock->getId());
    }

    public function testRetrieveBlockChildrenWithPagination(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertEquals(4, $options['query']['page_size']);
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_children_page_size_4_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $paginationRequest = (new PaginationRequest())
            ->setPageSize(4);

        /** @var BlockResults $paginationResponse */
        $paginationResponse = $client
            ->blocks()
            ->children()
            ->list('03cd5dca-84f7-456f-b7e6-aad92d5f69fd', $paginationRequest);

        $this->assertTrue($paginationResponse->isHasMore());
        $this->assertNotEmpty($paginationResponse->getNextCursor());
        $this->assertLessThanOrEqual(4, count($paginationResponse->getResults()));
    }

    public function testRetrieveBlockChildrenWithPaginationNextCursor(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertEquals('052e99f4-5a5e-4b2c-acd5-8ad240aeb719', $options['query']['start_cursor']);
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_children_page_size_4_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $paginationRequest = (new PaginationRequest())
            ->setPageSize(4)
            ->setStartCursor('052e99f4-5a5e-4b2c-acd5-8ad240aeb719');

        /** @var BlockResults $paginationResponse */
        $paginationResponse = $client
            ->blocks()
            ->children()
            ->list('03cd5dca-84f7-456f-b7e6-aad92d5f69fd', $paginationRequest);

        $this->assertNotNull($paginationResponse);
    }

    public function testAppendBlockChildren(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'PATCH') {
                $this->assertStringContainsString('blocks/03cd5dca-84f7-456f-b7e6-aad92d5f69fd/children', $url);

                /** @var array $body */
                $body = json_decode($options['body'], true);

                $this->assertArrayHasKey('children', $body);
                $this->assertArrayHasKey('object', $body['children'][0]);
                $this->assertArrayHasKey('type', $body['children'][0]);
                $this->assertArrayHasKey('heading_3', $body['children'][0]);
                $this->assertArrayHasKey('rich_text', $body['children'][0]['heading_3']);
                $this->assertNotEmpty($body['children'][0]['heading_3']['rich_text']);
                $this->assertStringContainsString(
                    'New title here',
                    $body['children'][0]['heading_3']['rich_text'][0]['text']['content'],
                );
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_children_page_size_4_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $richText = Text::fromContent('New title here');

        $heading3 = new Heading3Block();

        $heading3Property = new HeadingProperty();
        $heading3Property->setRichText([$richText]);
        $heading3->setHeading3($heading3Property);

        /** @var BlockResults $paginationResponse */
        $paginationResponse = $client
            ->blocks()
            ->children()
            ->append('03cd5dca-84f7-456f-b7e6-aad92d5f69fd', [$heading3]);

        $this->assertNotNull($paginationResponse);
        $this->assertInstanceOf(BlockResults::class, $paginationResponse);
    }

    public function testDeleteBlock(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('DELETE', $method);
            $this->assertStringContainsString('blocks/0c940186-ab70-4351-bb34-2d16f0635d49', $url);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var ParagraphBlock $block */
        $block = $client->blocks()->delete('0c940186-ab70-4351-bb34-2d16f0635d49');
        $this->assertNotNull($block);
        $this->assertInstanceOf(AbstractBlock::class, $block);
    }

    public function testRetrieveTableRowList(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertStringContainsString('blocks/64fecca8-8945-4d58-9a38-d9738e6b5a4e/children', $url);
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_table_row_list_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var BlockResults $paginationResponse */
        $paginationResponse = $client->blocks()->children()->list('64fecca8-8945-4d58-9a38-d9738e6b5a4e');

        $this->assertEquals('list', $paginationResponse->getObject());
        $this->assertGreaterThan(0, count($paginationResponse->getResults()));

        /** @var TableRowBlock $resultBlock */
        $resultBlock = $paginationResponse->getResults()[0];

        $this->assertEquals('table_row', $resultBlock->getType());
        $this->assertGreaterThan(0, count($resultBlock->getTableRow()->getCells()));

        /** @var Text $text */
        $text = $resultBlock->getTableRow()->getCells()[0];

        $this->assertInstanceOf(Text::class, $text);

        $this->assertEquals('Header 1', $text->getText()->getContent());
    }
}
