<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\UniqueIdPropertyConfiguration;

class UniqueIdPropertyObject extends AbstractPropertyObject
{
    protected ?UniqueIdPropertyConfiguration $uniqueId = null;

    public function __construct()
    {
        $this->uniqueId = new UniqueIdPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->uniqueId = isset($this->getRawData()['unique_id']) ?
            UniqueIdPropertyConfiguration::fromRawData((array) $this->getRawData()['unique_id']) :
            null;
    }

    public function getUniqueId(): ?UniqueIdPropertyConfiguration
    {
        return $this->uniqueId;
    }

    public function setUniqueId(?UniqueIdPropertyConfiguration $uniqueId): self
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }
}
