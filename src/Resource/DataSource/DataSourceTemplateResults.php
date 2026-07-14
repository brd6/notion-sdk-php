<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\DataSource;

use function array_map;

class DataSourceTemplateResults
{
    /**
     * @var array|DataSourceTemplate[]
     */
    protected array $templates = [];
    protected bool $hasMore = false;
    protected ?string $nextCursor = null;

    public static function fromRawData(array $rawData): self
    {
        $results = new self();

        $results->templates = isset($rawData['templates']) ? array_map(
            fn (array $templateRawData) => DataSourceTemplate::fromRawData($templateRawData),
            (array) $rawData['templates'],
        ) : [];
        $results->hasMore = (bool) ($rawData['has_more'] ?? false);
        $results->nextCursor = isset($rawData['next_cursor']) ? (string) $rawData['next_cursor'] : null;

        return $results;
    }

    /**
     * @return array|DataSourceTemplate[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function isHasMore(): bool
    {
        return $this->hasMore;
    }

    public function getNextCursor(): ?string
    {
        return $this->nextCursor;
    }
}
