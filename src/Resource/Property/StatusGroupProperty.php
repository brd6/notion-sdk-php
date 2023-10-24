<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class StatusGroupProperty extends AbstractProperty
{
    protected string $id = '';
    protected string $name = '';
    protected string $color = '';
    protected array $optionIds = [];

    public static function fromRawData(array $rawData): self
    {
        $property = new self();
        $property->id = isset($rawData['id']) ? (string) $rawData['id'] : '';
        $property->name = isset($rawData['name']) ? (string) $rawData['name'] : '';
        $property->color = isset($rawData['color']) ? (string) $rawData['color'] : '';
        $property->optionIds = isset($rawData['option_ids']) ? (array) $rawData['option_ids'] : [];

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getOptionIds(): array
    {
        return $this->optionIds;
    }

    public function setOptionIds(array $optionIds): self
    {
        $this->optionIds = $optionIds;

        return $this;
    }
}
