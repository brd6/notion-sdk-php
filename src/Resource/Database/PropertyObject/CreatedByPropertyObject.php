<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\CreatedByPropertyConfiguration;

class CreatedByPropertyObject extends AbstractPropertyObject
{
    protected ?CreatedByPropertyConfiguration $createdBy = null;

    public function __construct()
    {
        $this->createdBy = new CreatedByPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->createdBy = isset($this->getRawData()['created_by']) ?
            CreatedByPropertyConfiguration::fromRawData((array) $this->getRawData()['created_by']) :
            null;
    }

    public function getCreatedBy(): ?CreatedByPropertyConfiguration
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?CreatedByPropertyConfiguration $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
