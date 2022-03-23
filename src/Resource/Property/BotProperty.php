<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class BotProperty extends AbstractProperty
{
    protected ?OwnerProperty $owner = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->owner = OwnerProperty::fromRawData($rawData);

        return $property;
    }

    public function getOwner(): ?OwnerProperty
    {
        return $this->owner;
    }

    public function setOwner(OwnerProperty $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
