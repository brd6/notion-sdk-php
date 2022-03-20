<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\BlocksEndpoint;
use Brd6\NotionSdkPhp\Resource\Block\ChildPageBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\PartialUser;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\Test\NotionSdkPhp\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

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
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

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
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_child_page_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

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

        $this->assertInstanceOf(PartialUser::class, $block->getCreatedBy());
        $this->assertEquals('user', $block->getCreatedBy()->getObject());
        $this->assertNotEmpty($block->getCreatedBy()->getId());

        $this->assertNotEmpty($block->getCreatedTime());
        $this->assertNotEmpty($block->getLastEditedTime());
        $this->assertNotEmpty($block->getLastEditedTime());
    }

    public function testUpdateBlock(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
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

            return new MockResponse(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_200.json'),
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
}
