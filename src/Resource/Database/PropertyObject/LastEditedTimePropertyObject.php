<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\LastEditedTimePropertyConfiguration;

class LastEditedTimePropertyObject extends AbstractPropertyObject
{
    protected ?LastEditedTimePropertyConfiguration $lastEditedTime = null;

    public function __construct()
    {
        $this->lastEditedTime = new LastEditedTimePropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->lastEditedTime = isset($this->getRawData()['last_edited_time']) ?
            LastEditedTimePropertyConfiguration::fromRawData((array) $this->getRawData()['last_edited_time']) :
            null;
    }

    public function getLastEditedTime(): ?LastEditedTimePropertyConfiguration
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(?LastEditedTimePropertyConfiguration $lastEditedTime): self
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }
}
