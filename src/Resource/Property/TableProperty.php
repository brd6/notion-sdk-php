<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;

use function array_map;

class TableProperty extends AbstractProperty
{
    protected int $tableWidth = 0;
    protected bool $hasColumnHeader = false;
    protected bool $hasRowHeader = false;

    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->tableWidth = (int) $rawData['table_width'];
        $property->hasColumnHeader = (bool) $rawData['has_column_header'];
        $property->hasRowHeader = (bool) $rawData['has_row_header'];
        $property->children = isset($rawData['children']) ? array_map(
            fn (array $childRawData) => AbstractBlock::fromRawData($childRawData),
            (array) $rawData['children'],
        ) : [];

        return $property;
    }

    public function getTableWidth(): int
    {
        return $this->tableWidth;
    }

    public function setTableWidth(int $tableWidth): self
    {
        $this->tableWidth = $tableWidth;

        return $this;
    }

    public function isHasColumnHeader(): bool
    {
        return $this->hasColumnHeader;
    }

    public function setHasColumnHeader(bool $hasColumnHeader): self
    {
        $this->hasColumnHeader = $hasColumnHeader;

        return $this;
    }

    public function isHasRowHeader(): bool
    {
        return $this->hasRowHeader;
    }

    public function setHasRowHeader(bool $hasRowHeader): self
    {
        $this->hasRowHeader = $hasRowHeader;

        return $this;
    }

    /**
     * @return array|AbstractBlock[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array|AbstractBlock[] $children
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}
