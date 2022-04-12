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

class DatabasesEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function query(
        string $databaseId,
        ?DatabaseRequest $databaseRequest = null,
        ?PaginationRequest $paginationRequest = null
    ): AbstractPaginationResults {
        $requestParameters = (new RequestParameters())
            ->setPath("databases/$databaseId/query")
            ->setQuery($paginationRequest ? $paginationRequest->toArray() : [])
            ->setBody($databaseRequest ? $databaseRequest->toArray() : [])
            ->setMethod('POST');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }

    /**
     * @throws ApiResponseException
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
}
