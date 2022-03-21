<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\ColumnListProperty;

use function array_map;

class ColumnListBlock extends AbstractBlock
{
    /**
     * @var array|ColumnBlock[]
     */
    protected array $children = [];

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->children = isset($data['children']) ? array_map(
            fn (array $childRawData) => ColumnBlock::fromRawData($childRawData),
            (array) $data['children'],
        ) : [];
    }

    public function getColumnList(): ?ColumnListProperty
    {
        return $this->columnList;
    }

    public function setColumnList(?ColumnListProperty $columnList): self
    {
        $this->columnList = $columnList;

        return $this;
    }
}
