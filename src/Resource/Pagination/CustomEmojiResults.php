<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Resource\Property\CustomEmojiProperty;

use function array_map;

class CustomEmojiResults extends AbstractPaginationResults
{
    protected function initialize(): void
    {
        $this->results = isset($this->getRawData()['results']) ? array_map(
            fn (array $resultRawData) => CustomEmojiProperty::fromRawData($resultRawData),
            (array) $this->getRawData()['results'],
        ) : [];
    }

    /**
     * @return CustomEmojiProperty[]|array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
