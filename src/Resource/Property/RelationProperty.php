<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class RelationProperty extends AbstractProperty
{
    protected string $id = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();
        $property->id = isset($rawData['id']) ? (string) $rawData['id'] : null;

        return $property;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
