<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\RollupPropertyConfiguration;

class RollupPropertyObject extends AbstractPropertyObject
{
    protected ?RollupPropertyConfiguration $rollup = null;

    public function __construct()
    {
        $this->rollup = new RollupPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->rollup = RollupPropertyConfiguration::fromRawData($data);
    }

    public function getRollup(): ?RollupPropertyConfiguration
    {
        return $this->rollup;
    }

    public function setRollup(RollupPropertyConfiguration $rollup): self
    {
        $this->rollup = $rollup;

        return $this;
    }
}
