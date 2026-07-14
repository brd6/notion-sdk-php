<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\BlocksEndpoint;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\ChildPageBlock;
use Brd6\NotionSdkPhp\Resource\Block\Heading3Block;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesBlock;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesQueryRequest;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesQueryResults;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Block\TableRowBlock;
use Brd6\NotionSdkPhp\Resource\Pagination\BlockResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\MeetingNotesProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\NotionSdkPhp\Resource\UserInterface;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function array_keys;
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
                $this->assertArrayNotHasKey('archived', $body['children'][0]);
                $this->assertArrayNotHasKey('has_children', $body['children'][0]);
                $this->assertArrayNotHasKey('children', $body['children'][0]);
                $this->assertArrayHasKey('children', $body['children'][0]['heading_3']);
                $this->assertEquals(
                    'Nested paragraph',
                    $body['children'][0]['heading_3']['children'][0]['paragraph']['rich_text'][0]['text']['content'],
                );
                $this->assertArrayNotHasKey('archived', $body['children'][0]['heading_3']['children'][0]);
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

        $nestedParagraph = new ParagraphBlock();
        $nestedParagraphProperty = new ParagraphProperty();
        $nestedParagraphProperty->setRichText([Text::fromContent('Nested paragraph')]);
        $nestedParagraph->setParagraph($nestedParagraphProperty);
        $heading3->setChildren([$nestedParagraph]);

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
        $text = $resultBlock->getTableRow()->getCells()[0][0];

        $this->assertInstanceOf(Text::class, $text);

        $this->assertEquals('Header 1', $text->getText()->getContent());
    }

    public function testRetrieveTableRowListWithEmptyCells(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertStringContainsString('blocks/64fecca8-8945-4d58-9a38-d9738e6b5a4e/children', $url);
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_table_row_list_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        /** @var BlockResults $paginationResponse */
        $paginationResponse = $client->blocks()->children()->list('64fecca8-8945-4d58-9a38-d9738e6b5a4e');

        $this->assertEquals('list', $paginationResponse->getObject());

        /** @var TableRowBlock $resultBlock */
        $resultBlock = $paginationResponse->getResults()[0];
        $this->assertEquals('table_row', $resultBlock->getType());

        $cells = $resultBlock->getTableRow()->getCells();

        $this->assertInstanceOf(Text::class, $cells[0][0]);
        $this->assertEquals('Header 1', $cells[0][0]->getText()->getContent());
        $this->assertInstanceOf(Text::class, $cells[1][0]);
        $this->assertEquals('Header 2', $cells[1][0]->getText()->getContent());
        $this->assertEmpty($cells[2]);

        $resultBlock2 = $paginationResponse->getResults()[1];
        $cells2 = $resultBlock2->getTableRow()->getCells();
        $this->assertInstanceOf(Text::class, $cells2[0][0]);
        $this->assertEquals('Content', $cells2[0][0]->getText()->getContent());
        $this->assertEmpty($cells2[2]);
    }

    public function testUpdateBlockSendsInTrashOn20260311(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertArrayHasKey('in_trash', $body);
            $this->assertArrayNotHasKey('archived', $body);
            $this->assertArrayHasKey('paragraph', $body);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
                ['http_code' => 200],
            );
        });

        $options = (new ClientOptions())
            ->setNotionVersion(ClientOptions::NOTION_VERSION_2026_03_11)
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $paragraphProperty = new ParagraphProperty();
        $paragraphProperty->setRichText([Text::fromContent('Hello world!')]);

        $block = new ParagraphBlock();
        $block->setId('0c940186-ab70-4351-bb34-2d16f0635d49');
        $block->setParagraph($paragraphProperty);

        $client->blocks()->update($block);
    }

    public function testAppendBlockChildrenAfterBlockId(): void
    {
        $buildHttpClient = fn (callable $assertBody) => new MockHttpClient(
            function (string $method, string $url, array $options) use ($assertBody) {
                /** @var array $body */
                $body = json_decode($options['body'], true);
                $assertBody($body);

                return new MockResponseFactory(
                    (string) file_get_contents(
                        'tests/Fixtures/client_blocks_retrieve_block_children_page_size_4_200.json',
                    ),
                    ['http_code' => 200],
                );
            },
        );

        $buildBlock = static function (): Heading3Block {
            $heading3 = new Heading3Block();
            $heading3Property = new HeadingProperty();
            $heading3Property->setRichText([Text::fromContent('New title here')]);
            $heading3->setHeading3($heading3Property);

            return $heading3;
        };

        $legacyClient = new Client((new ClientOptions())->setHttpClient($buildHttpClient(
            function (array $body): void {
                $this->assertEquals('after-block-id', $body['after']);
                $this->assertArrayNotHasKey('position', $body);
            },
        )));
        $legacyClient->blocks()->children()->append('parent-block-id', [$buildBlock()], 'after-block-id');

        $currentClient = new Client(
            (new ClientOptions())
                ->setNotionVersion(ClientOptions::NOTION_VERSION_2026_03_11)
                ->setHttpClient($buildHttpClient(
                    function (array $body): void {
                        $this->assertEquals(
                            ['type' => 'after_block', 'after_block' => ['id' => 'after-block-id']],
                            $body['position'],
                        );
                        $this->assertArrayNotHasKey('after', $body);
                    },
                )),
        );
        $currentClient->blocks()->children()->append('parent-block-id', [$buildBlock()], 'after-block-id');

        $defaultClient = new Client((new ClientOptions())->setHttpClient($buildHttpClient(
            function (array $body): void {
                $this->assertEquals(['children'], array_keys($body));
            },
        )));
        $defaultClient->blocks()->children()->append('parent-block-id', [$buildBlock()]);
    }

    public function testQueryMeetingNotes(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString('blocks/meeting_notes/query', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertEquals('title', $body['filter']['property']);
            $this->assertEquals(
                [['property' => 'last_edited_time', 'direction' => 'descending']],
                $body['sort'],
            );
            $this->assertEquals(10, $body['limit']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_meeting_notes_query_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $queryRequest = (new MeetingNotesQueryRequest())
            ->setFilter([
                'property' => 'title',
                'filter' => [
                    'operator' => 'string_contains',
                    'value' => ['type' => 'exact', 'value' => 'Sync'],
                ],
            ])
            ->setSort([['property' => 'last_edited_time', 'direction' => 'descending']])
            ->setLimit(10);

        $results = $client->blocks()->queryMeetingNotes($queryRequest);

        $this->assertInstanceOf(MeetingNotesQueryResults::class, $results);
        $this->assertFalse($results->isHasMore());
        $this->assertCount(1, $results->getResults());

        $block = $results->getResults()[0];
        $this->assertInstanceOf(MeetingNotesBlock::class, $block);
        $this->assertEquals('Q3 Sync', $block->getMeetingNotes()->getTitle()[0]->getPlainText());
        $this->assertEquals(MeetingNotesProperty::STATUS_NOTES_READY, $block->getMeetingNotes()->getStatus());
    }

    public function testQueryMeetingNotesWithoutRequest(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('', $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_blocks_meeting_notes_query_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $results = $client->blocks()->queryMeetingNotes();

        $this->assertCount(1, $results->getResults());
    }
}
