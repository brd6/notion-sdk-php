<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Http\Client\Exception;

use function array_merge;

class CustomEmojisEndpoint extends AbstractEndpoint
{
    /**
     * Requires Notion-Version 2026-03-11.
     *
     * @param string|null $name filters custom emojis by exact name match
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function list(?string $name = null, ?PaginationRequest $paginationRequest = null): AbstractPaginationResults
    {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();

        $query = array_merge(
            $paginationRequest->toArray(),
            $name !== null ? ['name' => $name] : [],
        );

        $requestParameters = (new RequestParameters())
            ->setPath('custom_emojis')
            ->setQuery($query)
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }
}
