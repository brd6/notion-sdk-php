<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResponse;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;

use function array_map;

class BlocksChildrenEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function list(string $blockId, ?PaginationRequest $paginationRequest = null): AbstractPaginationResponse
    {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();

        $requestParameters = (new RequestParameters())
            ->setPath("blocks/$blockId/children")
            ->setQuery($paginationRequest->toJson())
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResponse::fromRawData($rawData);
    }

    /**
     * @param string $blockId
     * @param array|AbstractBlock[] $children
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function append(string $blockId, array $children): AbstractPaginationResponse
    {
        $childrenData = array_map(fn (AbstractBlock $block) => $block->toJson(), $children);

        $requestParameters = (new RequestParameters())
            ->setPath("blocks/$blockId/children")
            ->setBody([
                'children' => $childrenData,
            ])
            ->setMethod('PATCH');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResponse::fromRawData($rawData);
    }
}
