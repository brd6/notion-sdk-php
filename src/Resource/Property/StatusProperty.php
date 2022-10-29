<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class StatusProperty extends AbstractProperty
{
    protected ?string $id = null;
    protected ?string $name = null;
    protected ?string $color = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->id = isset($rawData['id']) ? (string)$rawData['id'] : null;
        $property->name = isset($rawData['name']) ? (string)$rawData['name'] : null;
        $property->color = isset($rawData['color']) ? (string)$rawData['color'] : null;

        return $property;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->name;
    }

    public function setEnd(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
