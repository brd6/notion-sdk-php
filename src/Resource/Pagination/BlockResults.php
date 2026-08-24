<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;

use function array_map;

class BlockResults extends AbstractPaginationResults
{
    public static function fromRawDataWithUnsupportedContentFallback(array $rawData): AbstractPaginationResults
    {
        if (!isset($rawData['object'], $rawData['type'])) {
            throw new InvalidPaginationResponseException();
        }

        if ($rawData['type'] !== 'block') {
            return parent::fromRawData($rawData);
        }

        $results = new self();
        $results->setRawData($rawData);
        $results->results = isset($rawData['results']) ? array_map(
            fn (array $resultRawData) => AbstractBlock::fromRawDataWithUnsupportedContentFallback($resultRawData),
            (array) $rawData['results'],
        ) : [];

        return $results;
    }

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initialize(): void
    {
        $this->results = isset($this->getRawData()['results']) ? array_map(
            fn (array $resultRawData) => AbstractBlock::fromRawData($resultRawData),
            (array) $this->getRawData()['results'],
        ) : [];
    }

    /**
     * @return AbstractBlock[]|array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
