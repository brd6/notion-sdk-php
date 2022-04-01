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
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
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
    public function list(string $blockId, ?PaginationRequest $paginationRequest = null): AbstractPaginationResults
    {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();

        $requestParameters = (new RequestParameters())
            ->setPath("blocks/$blockId/children")
            ->setQuery($paginationRequest->toArray())
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }

    /**
     * @param array|AbstractBlock[] $children
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function append(string $blockId, array $children): AbstractPaginationResults
    {
        $childrenData = array_map(fn (AbstractBlock $block) => $block->toArray(), $children);

        $requestParameters = (new RequestParameters())
            ->setPath("blocks/$blockId/children")
            ->setBody([
                'children' => $childrenData,
            ])
            ->setMethod('PATCH');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }
}
