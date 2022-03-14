<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function file_get_contents;

class ClientTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Client::class, new Client());
    }

    public function testRequestInvalidUrl(): void
    {
        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth');

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('invalid');

        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_invalid_url_400.json'),
                [
                    'http_code' => 400,
                ],
            ),
        ]);

        $options->setHttpClient($httpClient);

        $client = new Client($options);

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Invalid request URL');

        $client->request($params);
    }

    public function testRequestInvalidResponse(): void
    {
        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth');

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('pages/valid-id');

        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_invalid_response_500.json'),
                [
                    'http_code' => 500,
                ],
            ),
        ]);

        $options->setHttpClient($httpClient);

        $client = new Client($options);

        $this->expectException(HttpResponseException::class);
        $this->expectExceptionMessage('Request to Notion API failed with status: 500');

        $client->request($params);
    }

    public function testRequestInvalidResponseContent(): void
    {
        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth');

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('pages/valid-id');

        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_invalid_response_content.txt'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

        $options->setHttpClient($httpClient);

        $client = new Client($options);

        $this->expectException(RequestTimeoutException::class);
        $this->expectExceptionMessage('Request to Notion API has timed out');

        $client->request($params);
    }

    public function testRequestInvalidTokenApi(): void
    {
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_invalid_token_api_401.json'),
                [
                    'http_code' => 401,
                ],
            ),
        ]);

        $options = (new ClientOptions())
            ->setAuth('secret_invalid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('pages/valid-page-id');

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('API token is invalid');

        $client->request($params);
    }

    public function testRequestMissingVersion(): void
    {
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_missing_version_400.json'),
                [
                    'http_code' => 400,
                ],
            ),
        ]);

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('pages/valid-page-id');

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Notion-Version header failed validation');

        $client->request($params);
    }

    public function testRequestRetrievePage(): void
    {
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_retrieve_page_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('pages/b55c9c91-384d-452b-81db-d1ef79372b75');

        $rawData = $client->request($params);

        $this->assertArrayHasKey('id', $rawData);
        $this->assertArrayHasKey('object', $rawData);
        $this->assertEquals('b55c9c91-384d-452b-81db-d1ef79372b75', $rawData['id']);
        $this->assertEquals('page', $rawData['object']);
    }

    public function testRequestRetrieveInvalidPage(): void
    {
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_request_retrieve_page_404.json'),
                [
                    'http_code' => 404,
                ],
            ),
        ]);

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $params = new RequestParameters();
        $params
            ->setMethod('GET')
            ->setPath('pages/4a808e6e-8845-4d49-a447-fb2a4c460f0f');

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Could not find page with ID: 4a808e6e-8845-4d49-a447-fb2a4c460f0f');

        $client->request($params);
    }
}
