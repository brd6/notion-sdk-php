<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\AbstractPropertyItem;

use function array_map;

class PropertyItemResults extends AbstractPaginationResults
{
    protected function initialize(): void
    {
        $this->results = isset($this->getRawData()['results']) ? array_map(
            fn (array $resultRawData) => AbstractPropertyItem::fromRawData($resultRawData),
            (array) $this->getRawData()['results'],
        ) : [];
    }

    /**
     * @return AbstractPropertyItem[]|array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
