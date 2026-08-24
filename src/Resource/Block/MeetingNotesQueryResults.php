<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;

use function array_map;

class MeetingNotesQueryResults
{
    /**
     * @var array|AbstractBlock[]
     */
    protected array $results = [];
    protected bool $hasMore = false;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $queryResults = new self();

        $queryResults->results = isset($rawData['results']) ? array_map(
            fn (array $resultRawData) => AbstractBlock::fromRawData($resultRawData),
            (array) $rawData['results'],
        ) : [];
        $queryResults->hasMore = (bool) ($rawData['has_more'] ?? false);

        return $queryResults;
    }

    /**
     * @return array|AbstractBlock[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function setResults(array $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function isHasMore(): bool
    {
        return $this->hasMore;
    }

    public function setHasMore(bool $hasMore): self
    {
        $this->hasMore = $hasMore;

        return $this;
    }
}
