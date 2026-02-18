<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class SyncedFromProperty extends AbstractProperty
{
    protected string $type = '';
    protected ?string $blockId = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->type = (string) ($rawData['type'] ?? '');
        $property->blockId = (string) ($rawData['block_id'] ?? '');

        return $property;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): SyncedFromProperty
    {
        $this->type = $type;

        return $this;
    }

    public function getBlockId(): ?string
    {
        return $this->blockId;
    }

    public function setBlockId(?string $blockId): self
    {
        $this->blockId = $blockId;

        return $this;
    }
}
