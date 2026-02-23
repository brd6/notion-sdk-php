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
use Brd6\NotionSdkPhp\Resource\DataSource;
use Brd6\NotionSdkPhp\Resource\Database\DatabaseRequest;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Http\Client\Exception;

use function array_merge;

class DataSourcesEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function retrieve(string $dataSourceId): DataSource
    {
        $requestParameters = (new RequestParameters())
            ->setPath("data_sources/$dataSourceId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData($rawData);

        return $dataSource;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function query(
        string $dataSourceId,
        ?DatabaseRequest $databaseRequest = null,
        ?PaginationRequest $paginationRequest = null
    ): AbstractPaginationResults {
        $body = array_merge(
            $paginationRequest ? $paginationRequest->toArray() : [],
            $databaseRequest ? $databaseRequest->toArray() : [],
        );

        $requestParameters = (new RequestParameters())
            ->setPath("data_sources/$dataSourceId/query")
            ->setBody($body)
            ->setMethod('POST');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function update(DataSource $dataSource): DataSource
    {
        $data = self::normalizeEmptyPropertyConfigurations($dataSource->toArrayForUpdate());

        $requestParameters = (new RequestParameters())
            ->setPath("data_sources/{$dataSource->getId()}")
            ->setMethod('PATCH')
            ->setBody($data);

        $rawData = $this->getClient()->request($requestParameters);

        /** @var DataSource $dataSourceUpdated */
        $dataSourceUpdated = DataSource::fromRawData($rawData);

        return $dataSourceUpdated;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function create(DataSource $dataSource): DataSource
    {
        $data = self::normalizeEmptyPropertyConfigurations($dataSource->toArray());

        $requestParameters = (new RequestParameters())
            ->setPath('data_sources')
            ->setMethod('POST')
            ->setBody($data);

        $rawData = $this->getClient()->request($requestParameters);

        /** @var DataSource $dataSourceCreated */
        $dataSourceCreated = DataSource::fromRawData($rawData);

        return $dataSourceCreated;
    }
}
