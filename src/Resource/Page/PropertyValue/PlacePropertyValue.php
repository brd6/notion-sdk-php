<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\PlaceProperty;

class PlacePropertyValue extends AbstractPropertyValue
{
    protected ?PlaceProperty $place = null;

    protected function initialize(): void
    {
        $this->place = isset($this->getRawData()[$this->getType()]) ?
            PlaceProperty::fromRawData((array) $this->getRawData()[$this->getType()]) :
            null;
    }

    public function getPlace(): ?PlaceProperty
    {
        return $this->place;
    }

    public function setPlace(?PlaceProperty $place): self
    {
        $this->place = $place;

        return $this;
    }
}
