<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\StatusPropertyConfiguration;

class StatusPropertyObject extends AbstractPropertyObject
{
    protected ?StatusPropertyConfiguration $status = null;

    public function __construct()
    {
        $this->status = new StatusPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->status = isset($this->getRawData()['status']) ?
            StatusPropertyConfiguration::fromRawData((array) $this->getRawData()['status']) :
            null;
    }

    public function getStatus(): ?StatusPropertyConfiguration
    {
        return $this->status;
    }

    public function setStatus(?StatusPropertyConfiguration $status): self
    {
        $this->status = $status;

        return $this;
    }
}
