<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class PartialDatabaseProperty extends AbstractProperty
{
    protected string $id = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->id = (string) $rawData['id'];

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
