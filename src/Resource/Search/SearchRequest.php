<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Search;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

class SearchRequest extends AbstractJsonSerializable
{
    public const FILTER_VALUE_PAGE = 'page';
    public const FILTER_VALUE_DATA_SOURCE = 'data_source';

    /**
     * Pre-2025-09-03 filter value; on newer API versions use FILTER_VALUE_DATA_SOURCE.
     */
    public const FILTER_VALUE_DATABASE = 'database';

    protected array $filter = [];
    protected array $sort = [];
    protected ?string $query = null;

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function setFilter(array $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(array $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }
}
