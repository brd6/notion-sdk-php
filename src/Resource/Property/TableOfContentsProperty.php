<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class TableOfContentsProperty extends AbstractProperty
{
    protected string $color = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->color = (string) $rawData['color'];

        return $property;
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
}
