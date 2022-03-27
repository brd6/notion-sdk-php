<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\PagesEndpoint;
use Brd6\Test\NotionSdkPhp\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function file_get_contents;

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
}
