<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\CreatedTimePropertyConfiguration;

class CreatedTimePropertyObject extends AbstractPropertyObject
{
    protected ?CreatedTimePropertyConfiguration $createdTime = null;

    public function __construct()
    {
        $this->createdTime = new CreatedTimePropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->createdTime = isset($this->getRawData()['created_time']) ?
            CreatedTimePropertyConfiguration::fromRawData((array) $this->getRawData()['created_time']) :
            null;
    }

    public function getCreatedTime(): ?CreatedTimePropertyConfiguration
    {
        return $this->createdTime;
    }

    public function setCreatedTime(?CreatedTimePropertyConfiguration $createdTime): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }
}
