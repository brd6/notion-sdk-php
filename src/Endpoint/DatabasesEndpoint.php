<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Database\DatabaseRequest;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;

class DatabasesEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function query(string $databaseId, ?DatabaseRequest $databaseRequest = null): AbstractPaginationResults
    {
        $databaseRequest = $databaseRequest ?? new DatabaseRequest();

        $requestParameters = (new RequestParameters())
            ->setPath("databases/$databaseId/query")
            ->setQuery($databaseRequest->toArray())
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }
}
