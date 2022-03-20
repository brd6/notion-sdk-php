<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

class BlocksEndpoint extends AbstractEndpoint
{
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
     * @param AbstractBlock $block
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function update(AbstractBlock $block): AbstractBlock
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($block->getType());
        $getterMethodName = "get$typeFormatted";

        /** @var AbstractProperty $property */
        $property = $block->$getterMethodName();
        $data = $property->toJson();

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
}
