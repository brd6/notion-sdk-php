<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\DatePropertyConfiguration;

class DatePropertyObject extends AbstractPropertyObject
{
    protected ?DatePropertyConfiguration $date = null;

    public function __construct()
    {
        $this->date = new DatePropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->date = isset($this->getRawData()['date']) ?
            DatePropertyConfiguration::fromRawData((array) $this->getRawData()['date']) :
            null;
    }

    public function getDate(): ?DatePropertyConfiguration
    {
        return $this->date;
    }

    public function setDate(?DatePropertyConfiguration $date): self
    {
        $this->date = $date;

        return $this;
    }
}
