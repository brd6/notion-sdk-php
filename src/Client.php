<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

use Brd6\NotionSdkPhp\Constant\NotionErrorCodeConstant;
use Brd6\NotionSdkPhp\Endpoint\BlocksEndpoint;
use Brd6\NotionSdkPhp\Endpoint\DatabasesEndpoint;
use Brd6\NotionSdkPhp\Endpoint\PagesEndpoint;
use Brd6\NotionSdkPhp\Endpoint\SearchEndpoint;
use Brd6\NotionSdkPhp\Endpoint\UsersEndpoint;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\HttpClient\HttpClientFactory;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\Search\SearchRequest;
use Brd6\NotionSdkPhp\Util\UrlHelper;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Exception;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

use function count;
use function in_array;
use function json_decode;
use function json_encode;
use function json_last_error;
use function sprintf;
use function substr;

use const JSON_ERROR_NONE;

class Client
{
    private const MIN_STATUS_CODE_FOR_HTTP_EXCEPTION = 300;

    private ClientOptions $options;
    private HttpMethodsClientInterface $httpClient;
    private BlocksEndpoint $blocksEndpoint;
    private UsersEndpoint $usersEndpoint;
    private PagesEndpoint $pagesEndpoint;
    private DatabasesEndpoint $databasesEndpoint;
    private SearchEndpoint $searchEndpoint;

    public function __construct(?ClientOptions $options = null)
    {
        $this->options = $options ?? new ClientOptions();
        $this->initializeHttpClient();

        $this->blocksEndpoint = new BlocksEndpoint($this);
        $this->usersEndpoint = new UsersEndpoint($this);
        $this->pagesEndpoint = new PagesEndpoint($this);
        $this->databasesEndpoint = new DatabasesEndpoint($this);
        $this->searchEndpoint = new SearchEndpoint($this);
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws RuntimeException
     * @throws HttpResponseException
     * @throws RequestTimeoutException
     */
    public function request(RequestParameters $parameters): array
    {
        $path = $this->buildRequestPath($parameters);

        $body = null;
        if (count($parameters->getBody()) > 0) {
            /** @var string $body */
            $body = json_encode($parameters->getBody());
        }

        try {
            $response = $this->httpClient->send($parameters->getMethod(), $path, [], $body);
        } catch (RequestException $e) {
            if (!($e instanceof HttpException)) {
                throw new RequestTimeoutException();
            }

            throw $this->createInvalidHttpResponseStatusException($e->getResponse());
        }

        if ($response->getStatusCode() >= self::MIN_STATUS_CODE_FOR_HTTP_EXCEPTION) {
            throw $this->createInvalidHttpResponseStatusException($response);
        }

        return $this->transformResponseContentsToArray($response->getBody()->getContents());
    }

    private function buildRequestPath(RequestParameters $parameters): string
    {
        $path = $parameters->getPath();

        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        if (count($parameters->getQuery()) > 0) {
            $path .= sprintf('?%s', UrlHelper::buildQuery($parameters->getQuery()));
        }

        return $path;
    }

    /**
     * @return ApiResponseException|HttpResponseException
     */
    private function createInvalidHttpResponseStatusException(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $rawData = $this->transformResponseContentsToArray($response->getBody()->getContents());

        if ($this->isNotionClientError($rawData)) {
            $exception = new ApiResponseException($response->getStatusCode(), $headers, $rawData);
        } else {
            $exception = new HttpResponseException($response->getStatusCode(), $headers, $rawData);
        }

        return $exception;
    }

    /**
     * @throws RuntimeException
     */
    private function transformResponseContentsToArray(string $contents): array
    {
        /** @var array $rawData */
        $rawData = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(sprintf('Unable to parse response body into JSON: %s', json_last_error()));
        }

        return $rawData;
    }

    private function initializeHttpClient(): void
    {
        $this->httpClient = (new HttpClientFactory())->create($this->options);
    }

    private function isNotionClientError(array $rawData): bool
    {
        return isset($rawData['code']) &&
            in_array($rawData['code'], NotionErrorCodeConstant::API_ERROR_CODES);
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

    /**
     * @throws ApiResponseException
     * @throws InvalidPaginationResponseException
     * @throws UnsupportedPaginationResponseTypeException
     * @throws HttpResponseException
     * @throws RequestTimeoutException
     */
    public function search(
        ?SearchRequest $searchRequest = null,
        ?PaginationRequest $paginationRequest = null,
    ): AbstractPaginationResults {
        return $this->searchEndpoint->search($searchRequest, $paginationRequest);
    }
}
