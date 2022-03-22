<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractProperty;

use function array_map;

class SyncedBlockProperty extends AbstractProperty
{
    protected ?SyncedFromProperty $syncedFrom = null;

    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->children = isset($rawData['children']) ? array_map(
            fn (array $childRawData) => AbstractBlock::fromRawData($childRawData),
            (array) $rawData['children'],
        ) : [];

        $property->syncedFrom = isset($rawData['synced_from']) ?
            SyncedFromProperty::fromRawData((array) $rawData['synced_from']) :
            null;

        return $property;
    }

    public function getSyncedFrom(): ?SyncedFromProperty
    {
        return $this->syncedFrom;
    }

    public function setSyncedFrom(?SyncedFromProperty $syncedFrom): self
    {
        $this->syncedFrom = $syncedFrom;

        return $this;
    }

    /**
     * @return array|AbstractBlock[]
     */
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
