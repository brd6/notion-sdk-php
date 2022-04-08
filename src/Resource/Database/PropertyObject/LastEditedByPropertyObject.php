<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\LastEditedByPropertyConfiguration;

class LastEditedByPropertyObject extends AbstractPropertyObject
{
    protected ?LastEditedByPropertyConfiguration $lastEditedBy = null;

    public function __construct()
    {
        $this->lastEditedBy = new LastEditedByPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->lastEditedBy = isset($this->getRawData()['last_edited_by']) ?
            LastEditedByPropertyConfiguration::fromRawData((array) $this->getRawData()['last_edited_by']) :
            null;
    }

    public function getLastEditedBy(): ?LastEditedByPropertyConfiguration
    {
        return $this->lastEditedBy;
    }

    public function setLastEditedBy(?LastEditedByPropertyConfiguration $lastEditedBy): self
    {
        $this->lastEditedBy = $lastEditedBy;

        return $this;
    }
}
