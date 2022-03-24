<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;

use function array_map;

class BlockResponse extends AbstractPaginationResponse
{
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
