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
use Brd6\NotionSdkPhp\Resource\Search\SearchRequest;
use Http\Client\Exception;

use function array_merge;

class SearchEndpoint extends AbstractEndpoint
{
    /**
     * @param SearchRequest|null $searchRequest
     * @param PaginationRequest|null $paginationRequest
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     * @throws Exception
     */
    public function search(
        ?SearchRequest $searchRequest = null,
        ?PaginationRequest $paginationRequest = null
    ): AbstractPaginationResults {
        $body = array_merge(
            $searchRequest ? $searchRequest->toArray() : [],
            $paginationRequest ? $paginationRequest->toArray() : [],
        );

        $requestParameters = (new RequestParameters())
            ->setPath('search')
            ->setBody($body)
            ->setMethod('POST');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }
}
