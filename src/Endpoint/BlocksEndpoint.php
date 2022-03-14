<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Block;

class BlocksEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws RequestTimeoutException
     * @throws InvalidResourceTypeException
     */
    public function retrieve(string $blockId): Block
    {
        $requestParameters = (new RequestParameters())
            ->setPath("blocks/$blockId")
            ->setMethod('GET');

        $responseData = $this->getClient()->request($requestParameters);

        return Block::fromResponseData($responseData);
    }
}
