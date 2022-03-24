<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

class PaginationRequest extends AbstractJsonSerializable
{
    public const DEFAULT_PAGE_SIZE = 100;

    protected ?string $startCursor = null;
    protected int $pageSize = self::DEFAULT_PAGE_SIZE;

    public function getStartCursor(): ?string
    {
        return $this->startCursor;
    }

    /**
     * @param string|null $startCursor
     *
     * @return PaginationRequest
     */
    public function setStartCursor(?string $startCursor): self
    {
        $this->startCursor = $startCursor;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return PaginationRequest
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }
}
