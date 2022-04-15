<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Resource\Database;
use Brd6\NotionSdkPhp\Resource\Page;

use function array_map;

class PageOrDatabaseResults extends AbstractPaginationResults
{
    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     */
    protected function initialize(): void
    {
        $this->results = isset($this->getRawData()['results']) ? array_map(
            fn (array $resultRawData) => $resultRawData['object'] === Database::RESOURCE_TYPE
                ? Database::fromRawData($resultRawData)
                : Page::fromRawData($resultRawData),
            (array) $this->getRawData()['results'],
        ) : [];
    }

    /**
     * @return Page[]|Database[]|array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
