<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;

class BlocksEndpoint extends AbstractEndpoint
{
    private BlocksChildrenEndpoint $childrenEndpoint;

    public function __construct(Client $client)
    {
        parent::__construct($client);

        $this->childrenEndpoint = new BlocksChildrenEndpoint($client);
    }

    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     * @throws InvalidResourceException
     */
    public function retrieve(string $blockId): AbstractBlock
    {
        $requestParameters = (new RequestParameters())
            ->setPath("blocks/$blockId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractBlock::fromRawData($rawData);
    }

    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function update(AbstractBlock $block): AbstractBlock
    {
        $data = $block->propertyToJson();

        $requestParameters = (new RequestParameters())
            ->setPath("blocks/{$block->getId()}")
            ->setBody([
                $block->getType() => $data,
                'archived' => $block->isArchived(),
            ])
            ->setMethod('PATCH');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractBlock::fromRawData($rawData);
    }

    public function children(): BlocksChildrenEndpoint
    {
        return $this->childrenEndpoint;
    }
}
