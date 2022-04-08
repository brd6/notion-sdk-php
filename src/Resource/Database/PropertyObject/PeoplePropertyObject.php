<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\PeoplePropertyConfiguration;

class PeoplePropertyObject extends AbstractPropertyObject
{
    protected ?PeoplePropertyConfiguration $people = null;

    public function __construct()
    {
        $this->people = new PeoplePropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->people = isset($this->getRawData()['people']) ?
            PeoplePropertyConfiguration::fromRawData((array) $this->getRawData()['people']) :
            null;
    }

    public function getPeople(): ?PeoplePropertyConfiguration
    {
        return $this->people;
    }

    public function setPeople(?PeoplePropertyConfiguration $people): self
    {
        $this->people = $people;

        return $this;
    }
}
