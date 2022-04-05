<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;

use function array_map;

class SyncedBlockProperty extends AbstractProperty
{
    protected ?SyncedFromProperty $syncedFrom = null;

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
