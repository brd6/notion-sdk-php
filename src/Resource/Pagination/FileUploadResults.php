<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Resource\FileUpload;

use function array_map;

class FileUploadResults extends AbstractPaginationResults
{
    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     */
    protected function initialize(): void
    {
        $this->results = isset($this->getRawData()['results']) ? array_map(
            fn (array $resultRawData) => FileUpload::fromRawData($resultRawData),
            (array) $this->getRawData()['results'],
        ) : [];
    }

    /**
     * @return FileUpload[]|array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
