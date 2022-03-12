<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

use Brd6\NotionSdkPhp\Constant\NotionErrorCodeConstant;
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

    public function __construct(?ClientOptions $options = null)
    {
        $this->options = $options ?? new ClientOptions();
        $this->initializeHttpClient();
    }

    /**
     * @return array
     *
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
        } catch (TransportExceptionInterface) {
            throw new RequestTimeoutException();
        }

        try {
            return $response->toArray();
        } catch (TransportExceptionInterface | DecodingExceptionInterface) {
            throw new RequestTimeoutException();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            $response = $e->getResponse();
            $headers = $response->getHeaders(false);
            $responseData = $response->toArray(false);

            if ($this->isNotionClientError($responseData)) {
                throw new ApiResponseException($response->getStatusCode(), $headers, $responseData);
            }

            throw new HttpResponseException($response->getStatusCode(), $headers, $responseData);
        }
    }

    private function initializeHttpClient(): void
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

        $this->httpClient = HttpClient::create($httpOptions);
    }

    private function isNotionClientError(array $responseData): bool
    {
        return isset($responseData['code']) &&
            in_array($responseData['code'], NotionErrorCodeConstant::API_ERROR_CODES);
    }
}
