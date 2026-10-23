<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

class UniqueIdPropertyConfiguration extends AbstractProperty
{
    protected ?string $prefix = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->prefix = isset($rawData['prefix']) ? (string) $rawData['prefix'] : null;

        return $property;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }
}
