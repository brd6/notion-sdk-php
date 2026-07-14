<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\CustomEmojisEndpoint;
use Brd6\NotionSdkPhp\Resource\Pagination\CustomEmojiResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\Property\CustomEmojiProperty;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function count;
use function file_get_contents;

class CustomEmojisEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $customEmojis = new CustomEmojisEndpoint($client);

        $this->assertInstanceOf(CustomEmojisEndpoint::class, $client->customEmojis());
        $this->assertInstanceOf(CustomEmojisEndpoint::class, $customEmojis);
    }

    public function testListCustomEmojis(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('GET', $method);
            $this->assertStringContainsString('custom_emojis', $url);
            $this->assertArrayNotHasKey('name', $options['query']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_custom_emojis_list_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $results = $client->customEmojis()->list();

        $this->assertInstanceOf(CustomEmojiResults::class, $results);
        $this->assertEquals(2, count($results->getResults()));
        $this->assertFalse($results->isHasMore());

        $emoji = $results->getResults()[0];
        $this->assertInstanceOf(CustomEmojiProperty::class, $emoji);
        $this->assertEquals('party_parrot', $emoji->getName());
        $this->assertNotEmpty($emoji->getUrl());
        $this->assertNotEmpty($emoji->getId());
    }

    public function testListCustomEmojisWithNameAndPagination(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('shipit', $options['query']['name']);
            $this->assertEquals(10, (int) $options['query']['page_size']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_custom_emojis_list_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $client->customEmojis()->list('shipit', (new PaginationRequest())->setPageSize(10));
    }
}
