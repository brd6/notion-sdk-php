<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResponse;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;

class BlocksChildrenEndpoint extends AbstractEndpoint
{
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
}
