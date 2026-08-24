<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\AbstractUnsupportedNotionException;

class UnsupportedBlock extends AbstractBlock
{
    protected ?string $blockType = null;

    protected function initialize(): void
    {
        $this->initializeBlockState();

        if (isset($this->getRawData()['created_time'], $this->getRawData()['last_edited_time'])) {
            $this->initializeBlockTimes();
        }

        if (isset($this->getRawData()['created_by'])) {
            try {
                $this->initializeCreatedBy();
            } catch (AbstractUnsupportedNotionException $exception) {
                $this->createdBy = null;
            }
        }

        if (isset($this->getRawData()['last_edited_by'])) {
            try {
                $this->initializeLastEditedBy();
            } catch (AbstractUnsupportedNotionException $exception) {
                $this->lastEditedBy = null;
            }
        }

        $this->initializeChildren();
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
        $property = (array) ($this->getRawData()[$this->getType()] ?? []);
        unset($property['children']);

        return $property;
    }

    public function toArrayForCreate(): array
    {
        $data = $this->toArrayStrict(['object', 'type']);
        $data[$this->getType()] = $this->propertyToArray();

        return $this->addChildrenToCreateData($data);
    }
}
