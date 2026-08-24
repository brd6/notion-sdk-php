<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyTypeException;

class UnsupportedBlock extends AbstractBlock
{
    protected ?string $blockType = null;

    protected function initialize(): void
    {
        $this->type = (string) $this->getRawData()['type'];
        $this->initializeBlockProperty();
    }

    protected function initializeBlockProperty(): void
    {
        $data = (array) ($this->getRawData()['unsupported'] ?? []);
        $this->blockType = isset($data['block_type']) ? (string) $data['block_type'] : null;
    }

    public function getBlockType(): ?string
    {
        return $this->blockType;
    }

    public function setBlockType(?string $blockType): self
    {
        $this->blockType = $blockType;

        return $this;
    }

    public function propertyToArray(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }

    public function toArrayForCreate(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }
}
