<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\PlacePropertyConfiguration;

class PlacePropertyObject extends AbstractPropertyObject
{
    protected ?PlacePropertyConfiguration $place = null;

    public function __construct()
    {
        $this->place = new PlacePropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->place = isset($this->getRawData()['place']) ?
            PlacePropertyConfiguration::fromRawData((array) $this->getRawData()['place']) :
            null;
    }

    public function getPlace(): ?PlacePropertyConfiguration
    {
        return $this->place;
    }

    public function setPlace(?PlacePropertyConfiguration $place): self
    {
        $this->place = $place;

        return $this;
    }
}
