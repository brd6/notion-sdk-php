<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Database;
use Brd6\NotionSdkPhp\Resource\Database\DatabaseRequest;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Http\Client\Exception;

use function array_merge;

class DatabasesEndpoint extends AbstractEndpoint
{
    /**
     * @param string $databaseId
     * @param DatabaseRequest|null $databaseRequest
     * @param PaginationRequest|null $paginationRequest
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     * @throws Exception
     */
    public function query(
        string $databaseId,
        ?DatabaseRequest $databaseRequest = null,
        ?PaginationRequest $paginationRequest = null
    ): AbstractPaginationResults {
        $body = array_merge(
            $paginationRequest ? $paginationRequest->toArray() : [],
            $databaseRequest ? $databaseRequest->toArray() : [],
        );

        $requestParameters = (new RequestParameters())
            ->setPath("databases/$databaseId/query")
            ->setBody($body)
            ->setMethod('POST');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }

    /**
     * @param Database $database
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function create(Database $database): Database
    {
        $data = $database->toArray();

        $requestParameters = (new RequestParameters())
            ->setPath('databases')
            ->setMethod('POST')
            ->setBody($data);

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Database $databaseCreated */
        $databaseCreated = Database::fromRawData($rawData);

        return $databaseCreated;
    }

    /**
     * @param Database $database
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function update(Database $database): Database
    {
        $requestParameters = (new RequestParameters())
            ->setPath("databases/{$database->getId()}")
            ->setMethod('PATCH')
            ->setBody($database->toArrayForUpdate());

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Database $databaseUpdated */
        $databaseUpdated = Database::fromRawData($rawData);

        return $databaseUpdated;
    }

    /**
     * @param string $databaseId
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function retrieve(string $databaseId): Database
    {
        $requestParameters = (new RequestParameters())
            ->setPath("databases/$databaseId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Database $database */
        $database = Database::fromRawData($rawData);

        return $database;
    }
}
