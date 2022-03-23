<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use function array_map;

class ColumnListBlock extends AbstractBlock
{
    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->children = isset($data['children']) ? array_map(
            fn (array $childRawData) => AbstractBlock::fromRawData($childRawData),
            (array) $data['children'],
        ) : [];
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}
