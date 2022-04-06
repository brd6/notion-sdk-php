<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

use Brd6\NotionSdkPhp\Constant\NotionErrorCodeConstant;
use Brd6\NotionSdkPhp\Endpoint\BlocksEndpoint;
use Brd6\NotionSdkPhp\Endpoint\DatabasesEndpoint;
use Brd6\NotionSdkPhp\Endpoint\PagesEndpoint;
use Brd6\NotionSdkPhp\Endpoint\UsersEndpoint;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function count;
use function in_array;
use function strlen;

class Client
{
    private ClientOptions $options;
    private HttpClientInterface $httpClient;
    private BlocksEndpoint $blocksEndpoint;
    private UsersEndpoint $usersEndpoint;
    private PagesEndpoint $pagesEndpoint;
    private DatabasesEndpoint $databasesEndpoint;

    public function __construct(?ClientOptions $options = null)
    {
        $this->options = $options ?? new ClientOptions();
        $this->initializeHttpClient();

        $this->blocksEndpoint = new BlocksEndpoint($this);
        $this->usersEndpoint = new UsersEndpoint($this);
        $this->pagesEndpoint = new PagesEndpoint($this);
        $this->databasesEndpoint = new DatabasesEndpoint($this);
    }

    /**
     * @throws ApiResponseException
     * @throws RequestTimeoutException
     * @throws HttpResponseException
     */
    public function request(RequestParameters $parameters): array
    {
        $httpOptions = [];

        if (count($parameters->getQuery()) > 0) {
            $httpOptions['query'] = $parameters->getQuery();
        }

        if (count($parameters->getBody()) > 0) {
            $httpOptions['json'] = $parameters->getBody();
        }

        try {
            $response = $this->httpClient->request($parameters->getMethod(), $parameters->getPath(), $httpOptions);

            return $response->toArray();
        } catch (TransportExceptionInterface | DecodingExceptionInterface $e) {
            throw new RequestTimeoutException();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            $response = $e->getResponse();
            $headers = $response->getHeaders(false);
            $rawData = $response->toArray(false);

            if ($this->isNotionClientError($rawData)) {
                throw new ApiResponseException($response->getStatusCode(), $headers, $rawData);
            }

            throw new HttpResponseException($response->getStatusCode(), $headers, $rawData);
        }
    }

    private function initializeHttpClient(): void
    {
        $httpClient = $this->options->getHttpClient();

        if ($httpClient === null) {
            $httpClient = HttpClient::create();
        }

        $this->httpClient = $httpClient->withOptions($this->getDefaultHttpOptions());
    }

    private function isNotionClientError(array $rawData): bool
    {
        return isset($rawData['code']) &&
            in_array($rawData['code'], NotionErrorCodeConstant::API_ERROR_CODES);
    }

    private function getDefaultHttpOptions(): array
    {
        $httpOptions = [
            'base_uri' => $this->options->getBaseUrl(),
            'timeout' => $this->options->getTimeout(),
            'headers' => [
                'Notion-Version' => $this->options->getNotionVersion(),
                'User-Agent' => 'brd6/notion-sdk-php',
            ],
        ];

        if (strlen($this->options->getAuth()) > 0) {
            $httpOptions['auth_bearer'] = $this->options->getAuth();
        }

        return $httpOptions;
    }

    public function blocks(): BlocksEndpoint
    {
        return $this->blocksEndpoint;
    }

    public function users(): UsersEndpoint
    {
        return $this->usersEndpoint;
    }

    public function pages(): PagesEndpoint
    {
        return $this->pagesEndpoint;
    }

    public function databases(): DatabasesEndpoint
    {
        return $this->databasesEndpoint;
    }
}
